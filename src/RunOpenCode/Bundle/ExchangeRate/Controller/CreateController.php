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

use RunOpenCode\Bundle\ExchangeRate\Form\FormType;
use RunOpenCode\Bundle\ExchangeRate\Security\AccessVoter;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Model\Rate as ExchangeRate;
use RunOpenCode\Bundle\ExchangeRate\Form\Dto\Rate as DtoRate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CreateController
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Controller
 */
class CreateController extends Controller
{
    /**
     * Main controller action
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted(AccessVoter::CREATE, RateInterface::class)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->getForm();

        if (true === $this->handleForm($form, $request)) {
            return $this->redirectAfterSuccess();
        }

        return $this->render('@ExchangeRate/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Handle form submission.
     *
     * @param Form $form
     * @param Request $request
     *
     * @return bool TRUE if successful
     */
    protected function handleForm(Form $form, Request $request)
    {
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return false;
        }

        /**
         * @var ExchangeRate $rate
         */
        $rate = $form->getData()->toRate();

        if ($this->get('runopencode.exchange_rate.repository')->has($rate->getSourceName(), $rate->getCurrencyCode(), $rate->getDate(), $rate->getRateType())) {
            $form->addError(new FormError($this->get('translator')->trans('flash.create.error.exists', [], 'runopencode_exchange_rate'), 'flash.create.error.exits'));
        }

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
    protected function getForm()
    {
        return $this->createForm($this->getFormType(), new DtoRate(null, new \DateTime(), null, $this->getParameter('runopencode.exchange_rate.base_currency')));
    }

    /**
     * Save rate.
     *
     * @param ExchangeRate $rate
     * @return TRUE if successful.
     */
    protected function save(ExchangeRate $rate)
    {
        try {
            $this->get('runopencode.exchange_rate.repository')->save([$rate]);
            $this->addFlash('success', $this->get('translator')->trans('flash.create.success', [], 'runopencode_exchange_rate'));
            return true;
        } catch (\Exception $e) {
            $this->addFlash('error', $this->get('translator')->trans('flash.create.error.unknown', [], 'runopencode_exchange_rate'));
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
