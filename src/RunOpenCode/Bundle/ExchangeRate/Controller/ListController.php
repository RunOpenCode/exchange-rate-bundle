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

use RunOpenCode\Bundle\ExchangeRate\Form\FilterType;
use RunOpenCode\Bundle\ExchangeRate\Security\AccessVoter;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ListController
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Controller
 */
class ListController extends Controller
{
    /**
     * Main controller action.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted(AccessVoter::VIEW, RateInterface::class)) {
            throw $this->createAccessDeniedException();
        }

        $filterForm = $this->getFilterForm($request);

        return $this->render('@ExchangeRate/list.html.twig', [
            'rates' => $this->getRates($filterForm),
            'form' => $filterForm->createView(),
        ]);
    }

    /**
     * Get rates for list view. Process filters if submitted.
     *
     * @param Form $filterForm
     *
     * @return \RunOpenCode\ExchangeRate\Contract\RateInterface[]
     */
    protected function getRates(Form $filterForm)
    {
        /**
         * @var \RunOpenCode\ExchangeRate\Contract\RepositoryInterface $repository
         */
        $repository = $this->get('runopencode.exchange_rate.repository');

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            return $repository->all($filterForm->getData());
        }

        return $repository->all();
    }

    /**
     * Get filter form
     *
     * @param Request $request
     *
     * @return Form
     */
    protected function getFilterForm(Request $request)
    {
        $filter = $this->createForm($this->getFilterFormType());

        $filter->handleRequest($request);

        return $filter;
    }

    /**
     * Get FQCN of FilterType form.
     *
     * @return string
     */
    protected function getFilterFormType()
    {
        return FilterType::class;
    }
}
