<?php

namespace RunOpenCode\Bundle\ExchangeRate\Controller;

use RunOpenCode\Bundle\ExchangeRate\Form\Type\NewType;
use RunOpenCode\ExchangeRate\Contract\RepositoryInterface;
use RunOpenCode\Bundle\ExchangeRate\Model\Rate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ExchangeRateController extends Controller
{
    protected $repository;

    protected $baseCurrency;

    protected $settings;

    public function __construct(RepositoryInterface $repository, $baseCurrency, $settings)
    {
        $this->repository = $repository;
        $this->baseCurrency = $baseCurrency;
        $this->settings = $settings;
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

            $this->repository->save(array(
                $form->getData()
            ));

            $this->get('session')->getFlashBag()->add('success', 'run_open_code.exchange_rate.flash.new.success');
            $this->redirectToRoute('roc_exchange_rate_list');
        }

        return $this->render($this->settings['new'], array(
            'base_template' => $this->settings['base_template'],
            'form' => $form->createView()
        ));
    }

    protected function getNewRate()
    {
        $rate = new Rate(null, null, $this->baseCurrency, null, null, $this->baseCurrency, null, null);
        $rate->setBaseCurrencyCode($this->baseCurrency);

        return $rate;
    }

}
