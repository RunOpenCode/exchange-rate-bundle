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

use RunOpenCode\Bundle\ExchangeRate\Security\AccessVoter;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Contract\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * Class DeleteController
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Controller
 */
class DeleteController extends Controller
{
    /**
     * Main controller action
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('@ExchangeRate/delete.html.twig', [
            'rate' => $this->getRateFromRequest($request)
        ]);
    }

    /**
     * Execute delete action.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request)
    {
        if (!$this->isCsrfTokenValid($request->getRequestUri(), $request->get('_csrf_token'))) {
            throw new InvalidCsrfTokenException();
        }

        /**
         * @var \RunOpenCode\ExchangeRate\Contract\RateInterface $rate
         */
        $rate = $this->getRateFromRequest($request);

        if (!$this->delete($rate)) {
            return $this->indexAction($request);
        }

        return $this->redirectAfterSuccess();
    }

    /**
     * Get rate from request
     *
     * @param Request $request
     *
     * @return \RunOpenCode\ExchangeRate\Contract\RateInterface
     */
    protected function getRateFromRequest(Request $request)
    {
        $source = $request->get('source');
        $rateType = $request->get('rate_type');
        $currencyCode = $request->get('currency_code');
        $date = \DateTime::createFromFormat('Y-m-d', $request->get('date'));

        /**
         * @var RepositoryInterface $repository
         */
        $repository = $this->get('runopencode.exchange_rate.repository');

        if (!$repository->has($source, $currencyCode, $date, $rateType)) {
            throw $this->createNotFoundException();
        }

        $rate = $repository->get($source, $currencyCode, $date, $rateType);

        if (!$this->isGranted(AccessVoter::DELETE, $rate)) {
            throw $this->createAccessDeniedException();
        }

        return $rate;
    }

    /**
     * Save rate.
     *
     * @param RateInterface $rate
     *
     * @return TRUE if successful.
     */
    protected function delete(RateInterface $rate)
    {
        try {
            $this->get('runopencode.exchange_rate.repository')->delete([$rate]);
            $this->addFlash('success', $this->get('translator')->trans('flash.delete.success', [], 'runopencode_exchange_rate'));
            return true;
        } catch (\Exception $e) {
            $this->addFlash('error', $this->get('translator')->trans('flash.delete.error.unknown', [], 'runopencode_exchange_rate'));
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
