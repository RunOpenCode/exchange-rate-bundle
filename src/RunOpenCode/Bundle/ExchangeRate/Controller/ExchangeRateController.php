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
use RunOpenCode\Bundle\ExchangeRate\Form\Type\NewType;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Contract\RepositoryInterface;
use RunOpenCode\Bundle\ExchangeRate\Model\Rate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    protected $settings;

    public function __construct(
        RepositoryInterface $repository,
        $baseCurrency,
        $settings
    ) {
        $this->repository = $repository;
        $this->baseCurrency = $baseCurrency;
        $this->settings = $settings;
    }

    /**
     * List rates.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted(array('ROLE_EXCHANGE_RATE_MANAGER', 'ROLE_EXCHANGE_RATE_LIST'));

        return $this->render($this->settings['list'], array(
            'base_template' => $this->settings['base_template'],
            'rates' => $this->repository->all(),
            'date_format' => $this->settings['date_format'],
            'time_format' => $this->settings['time_format'],
            'secure' => $this->settings['secure']
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
        $this->denyAccessUnlessGranted(array('ROLE_EXCHANGE_RATE_MANAGER', 'ROLE_EXCHANGE_RATE_CREATE'));

        $form = $this->createForm($this->getNewFormTypeFQCN(), $this->getNewRate());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var RateInterface $rate
             */
            $rate = $form->getData();

            if ($this->repository->has($rate->getCurrencyCode(), $rate->getDate(), $rate->getRateType())) {
                $form->addError(new FormError($this->get('translator')->trans('exchange_rate.form.error.new_exists', array(), 'roc_exchange_rate')));
            } else {
                $this->repository->save(array(
                    $form->getData()
                ));

                $this->get('session')->getFlashBag()->add('success', 'exchange_rate.flash.new.success');
                return $this->redirectToRoute('roc_exchange_rate_list');
            }
        }

        return $this->render($this->settings['new'], array(
            'base_template' => $this->settings['base_template'],
            'form' => $form->createView(),
            'secure' => $this->settings['secure']
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
        $this->denyAccessUnlessGranted(array('ROLE_EXCHANGE_RATE_MANAGER', 'ROLE_EXCHANGE_RATE_EDIT'));

        $form = $this->createForm($this->getEditFormTypeFQCN(), $this->getRateFromRequest($request));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->repository->save(array(
                $form->getData()
            ));

            $this->get('session')->getFlashBag()->add('success', 'exchange_rate.flash.edit.success');
            return $this->redirectToRoute('roc_exchange_rate_list');
        }

        return $this->render($this->settings['edit'], array(
            'base_template' => $this->settings['base_template'],
            'form' => $form->createView(),
            'secure' => $this->settings['secure']
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
        $this->denyAccessUnlessGranted(array('ROLE_EXCHANGE_RATE_MANAGER', 'ROLE_EXCHANGE_RATE_DELETE'));

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
     * @return static
     */
    protected function getRateFromRequest(Request $request)
    {
        if (!$this->repository->has($request->get('source'), $request->get('currency_code'), \DateTime::createFromFormat('Y-m-d', $request->get('date')), $request->get('rate_type'))) {
            throw new NotFoundHttpException();
        }

        return Rate::fromRateInterface($this->repository->get($request->get('source'), $request->get('currency_code'), \DateTime::createFromFormat('Y-m-d', $request->get('date')), $request->get('rate_type')));
    }

    /**
     * Get FQDN of NewType form.
     *
     * @return string
     */
    protected function getNewFormTypeFQCN()
    {
        return NewType::class;
    }

    /**
     * Get FQDN of EditType form.
     *
     * @return string
     */
    protected function getEditFormTypeFQCN()
    {
        return EditType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function denyAccessUnlessGranted($attributes, $object = null, $message = 'Access Denied.')
    {
        if ($this->settings['secure']) {
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
