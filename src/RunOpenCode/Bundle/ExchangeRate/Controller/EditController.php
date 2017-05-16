<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Controller;

use RunOpenCode\Bundle\ExchangeRate\Form\Dto\Rate as DtoRate;
use RunOpenCode\Bundle\ExchangeRate\Form\FormType;
use RunOpenCode\Bundle\ExchangeRate\Security\AccessVoter;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Contract\RepositoryInterface;
use RunOpenCode\ExchangeRate\Model\Rate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EditController
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Controller
 */
class EditController extends Controller
{
    /**
     * Main controller action
     *
     * @param Request $request
     * @param $source
     * @param $rateType
     * @param $currencyCode
     * @param \DateTime $date
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function indexAction(Request $request, $source, $rateType, $currencyCode, \DateTime $date)
    {
        /**
         * @var RepositoryInterface $repository
         */
        $repository = $this->get('runopencode.exchange_rate.repository');

        if ($repository->has($source, $currencyCode, $date, $rateType)) {
            return $this->createNotFoundException();
        }

        $rate = $repository->get($source, $currencyCode, $date, $rateType);

        if (!$this->isGranted(AccessVoter::EDIT, $rate)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->getForm($rate);

        if (true === $this->handleForm($form, $request, $rate)) {
            return $this->redirectAfterSuccess();
        }

        return $this->render('@ExchangeRate/edit.html.twig', [
            'form' => $form->createView(),
            'rate' => $rate
        ]);
    }

    /**
     * Handle form submission.
     *
     * @param Form $form
     * @param Request $request
     * @param RateInterface $rate
     *
     * @return bool TRUE if successful
     */
    protected function handleForm(Form $form, Request $request, RateInterface $exchangeRate)
    {
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return false;
        }

        /**
         * @var Rate $rate
         */
        $rate = $form->getData()->toRate($exchangeRate);

        if (!$form->isValid()) {
            $this->addFlash('error', $this->get('translator')->trans('flash.form.error', [], 'runopencode_exchange_rate'));
            return false;
        }

        return $this->save($rate);
    }

    /**
     * Get FQCN of FormType form.
     *
     * @return string
     */
    protected function getFormType()
    {
        return FormType::class;
    }

    /**
     * Get form.
     *
     * @return Form
     */
    protected function getForm(RateInterface $rate)
    {
        return $this->createForm($this->getFormType(), DtoRate::fromRateInterface($rate));
    }

    /**
     * Save rate.
     *
     * @param Rate $rate
     * @return TRUE if successful.
     */
    protected function save(Rate $rate)
    {
        try {
            $this->get('runopencode.exchange_rate.repository')->save([$rate]);
            $this->addFlash('success', $this->get('translator')->trans('flash.edit.success', [], 'runopencode_exchange_rate'));
            return true;
        } catch (\Exception $e) {
            $this->addFlash('error', $this->get('translator')->trans('flash.edit.error.unknown', [], 'runopencode_exchange_rate'));
            return false;
        }
    }

    /**
     * Redirect after success.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectAfterSuccess()
    {
        return $this->redirectToRoute('runopencode_exchange_rate_list');
    }
}
