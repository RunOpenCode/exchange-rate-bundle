<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Controller;

use RunOpenCode\Bundle\ExchangeRate\Form\Type\EditType;
use RunOpenCode\Bundle\ExchangeRate\Form\Type\FilterType;
use RunOpenCode\Bundle\ExchangeRate\Form\Type\NewType;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Contract\RepositoryInterface;
use RunOpenCode\Bundle\ExchangeRate\Model\Rate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * Class ExchangeRateController
 *
 * Default exchange rate controller.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Controller
 */
class ExchangeRateController extends Controller
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    protected $baseCurrency;

    /**
     * @var array
     */
    protected $templates;

    /**
     * @var array
     */
    protected $accessRoles;

    public function __construct(
        RepositoryInterface $repository,
        $baseCurrency,
        $templates,
        $accessRoles
    ) {
        $this->repository = $repository;
        $this->baseCurrency = $baseCurrency;
        $this->templates = $templates;
        $this->accessRoles = $accessRoles;
    }

    /**
     * List rates.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted($this->accessRoles['list']);

        $filter = $this->getFilterForm($request);

        return $this->render($this->templates['list'], array(
            'base_template' => $this->templates['base_template'],
            'filter' => $filter->createView(),
            'rates' => $this->getListData($filter),
            'date_format' => $this->templates['date_format'],
            'time_format' => $this->templates['time_format'],
            'secure' => $this->templates['secure']
        ));
    }

    /**
     * Add new rate.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted($this->accessRoles['create']);

        $form = $this->createForm($this->getNewFormType(), $this->getNewRate());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var RateInterface $rate
             */
            $rate = $form->getData();

            if ($this->repository->has($rate->getSourceName(), $rate->getCurrencyCode(), $rate->getDate(), $rate->getRateType())) {
                $form->addError(new FormError($this->get('translator')->trans('exchange_rate.form.error.new_exists', array(), 'roc_exchange_rate')));
            } else {
                $this->repository->save(array(
                    $form->getData()
                ));

                $this->get('session')->getFlashBag()->add('success', 'exchange_rate.flash.new.success');
                return $this->redirectToRoute('roc_exchange_rate_list');
            }
        }

        return $this->render($this->templates['new'], array(
            'base_template' => $this->templates['base_template'],
            'form' => $form->createView(),
            'secure' => $this->templates['secure']
        ));
    }

    /**
     * Edit rate.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $this->denyAccessUnlessGranted($this->accessRoles['edit']);

        $form = $this->createForm($this->getEditFormType(), $this->getRateFromRequest($request));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->repository->save(array(
                $form->getData()
            ));

            $this->get('session')->getFlashBag()->add('success', 'exchange_rate.flash.edit.success');
            return $this->redirectToRoute('roc_exchange_rate_list');
        }

        return $this->render($this->templates['edit'], array(
            'base_template' => $this->templates['base_template'],
            'form' => $form->createView(),
            'secure' => $this->templates['secure']
        ));
    }

    /**
     * Delete rate.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $this->denyAccessUnlessGranted($this->accessRoles['delete']);

        if (!$this->isCsrfTokenValid($request->getRequestUri(), $request->get('_csrf_token'))) {
            throw new InvalidCsrfTokenException;
        }

        $rate = $this->getRateFromRequest($request);

        $this->repository->delete(array($rate));

        $this->get('session')->getFlashBag()->add('success', 'exchange_rate.flash.delete.success');
        return $this->redirectToRoute('roc_exchange_rate_list');
    }

    /**
     * Get new rate object for new form.
     *
     * @return Rate
     */
    protected function getNewRate()
    {
        $rate = new Rate(null, null, $this->baseCurrency, null, null, $this->baseCurrency, null, null);
        $rate->setBaseCurrencyCode($this->baseCurrency);

        return $rate;
    }

    /**
     * Find rate based on values of request parameters.
     *
     * @param Request $request
     * @return RateInterface
     */
    protected function getRateFromRequest(Request $request)
    {
        if (!$this->repository->has($request->get('source'), $request->get('currency_code'), \DateTime::createFromFormat('Y-m-d', $request->get('date')), $request->get('rate_type'))) {
            throw new NotFoundHttpException();
        }

        return Rate::fromRateInterface($this->repository->get($request->get('source'), $request->get('currency_code'), \DateTime::createFromFormat('Y-m-d', $request->get('date')), $request->get('rate_type')));
    }

    /**
     * Get FQCN of NewType form.
     *
     * @return string
     */
    protected function getNewFormType()
    {
        return NewType::class;
    }

    /**
     * Get FQCN of EditType form.
     *
     * @return string
     */
    protected function getEditFormType()
    {
        return EditType::class;
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

    /**
     * Get filter form
     *
     * @param Request $request
     * @return Form
     */
    protected function getFilterForm(Request $request)
    {
        $filter = $this->createForm($this->getFilterFormType());

        $filter->handleRequest($request);

        return $filter;
    }

    /**
     * Get list data. Process filters if submitted.
     *
     * @param Form $filter
     * @return \RunOpenCode\ExchangeRate\Contract\RateInterface[]
     */
    protected function getListData(Form $filter)
    {
        if ($filter->isSubmitted() && $filter->isValid()) {
            return $this->repository->all($filter->getData());
        }

        return $this->repository->all();
    }

    /**
     * {@inheritdoc}
     */
    protected function denyAccessUnlessGranted($attributes, $object = null, $message = 'Access Denied.')
    {
        if ($this->templates['secure']) {
            if (!is_array($attributes)) {
                $attributes = array($attributes);
            }

            $granted = false;

            foreach ($attributes as $attribute) {
                $granted |= $this->isGranted($attribute, $object);
            }

            if (!$granted) {
                throw $this->createAccessDeniedException($message);
            }
        }
    }
}
