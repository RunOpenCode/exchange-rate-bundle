<?php

namespace RunOpenCode\Bundle\ExchangeRate\Controller;

use RunOpenCode\Bundle\ExchangeRate\Form\Type\NewType;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Contract\RepositoryInterface;
use RunOpenCode\Bundle\ExchangeRate\Model\Rate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ExchangeRateController extends Controller
{
    protected $repository;

    protected $baseCurrency;

    protected $settings;

    protected $csrfTokenManager;

    protected $translator;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, TranslatorInterface $translator, RepositoryInterface $repository, $baseCurrency, $settings)
    {
        $this->repository = $repository;
        $this->baseCurrency = $baseCurrency;
        $this->settings = $settings;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->translator = $translator;
    }

    public function indexAction()
    {
        return $this->render($this->settings['list'], array(
            'base_template' => $this->settings['base_template'],
            'rates' => $this->repository->all(),
            'date_format' => $this->settings['date_format'],
            'date_time_format' => $this->settings['date_time_format']
        ));
    }

    public function newAction(Request $request)
    {
        $form = $this->createForm(NewType::class, $this->getNewRate());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var RateInterface $rate
             */
            $rate = $form->getData();

            if ($this->repository->has($rate->getCurrencyCode(), $rate->getDate(), $rate->getRateType())) {
                $form->addError(new FormError($this->translator->trans('exchange_rate.form.error.new_exists', array(), 'roc_exchange_rate')));
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
            'form' => $form->createView()
        ));
    }

    public function editAction(Request $request)
    {
        $form = $this->createForm(NewType::class, $this->getRateFromRequest($request));

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
            'form' => $form->createView()
        ));
    }

    public function deleteAction(Request $request)
    {
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($request->getRequestUri(), $request->get('_csrf_token')))) {
            throw new InvalidCsrfTokenException;
        }

        $rate = $this->getRateFromRequest($request);

        $this->repository->delete(array($rate));

        $this->get('session')->getFlashBag()->add('success', 'exchange_rate.flash.delete.success');
        return $this->redirectToRoute('roc_exchange_rate_list');
    }

    protected function getNewRate()
    {
        $rate = new Rate(null, null, $this->baseCurrency, null, null, $this->baseCurrency, null, null);
        $rate->setBaseCurrencyCode($this->baseCurrency);

        return $rate;
    }

    protected function getRateFromRequest(Request $request)
    {
        if (!$this->repository->has($request->get('currency_code'), \DateTime::createFromFormat('Y-m-d', $request->get('date')), $request->get('rate_type'))) {
            throw new NotFoundHttpException();
        }

        return Rate::fromRateInterface($this->repository->get($request->get('currency_code'), \DateTime::createFromFormat('Y-m-d', $request->get('date')), $request->get('rate_type')));
    }
}
