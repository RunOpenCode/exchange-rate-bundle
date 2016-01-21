<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Form;

use RunOpenCode\Bundle\ExchangeRate\Form\Type\RateType;
use RunOpenCode\Bundle\ExchangeRate\Model\Rate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BaseType
 *
 * Base exchange rate form.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Type
 */
abstract class BaseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
            'label' => 'exchange_rate.form.fields.value',
            'translation_domain' => 'roc_exchange_rate'
        ));

        $builder->add('date', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
            'label' => 'exchange_rate.form.fields.date',
            'translation_domain' => 'roc_exchange_rate'
        ));

        $builder->add('rate', RateType::class, array(
            'mapped' => false,
            'label' => 'exchange_rate.form.fields.rate',
            'translation_domain' => 'roc_exchange_rate'
        ));

        $builder->addEventListener(FormEvents::POST_SET_DATA, \Closure::bind(function(FormEvent $event) {
            $this->onPostSetData($event);
        }, $this));

        $builder->addEventListener(FormEvents::SUBMIT, \Closure::bind(function(FormEvent $event) {
            $this->onSubmit($event);
        }, $this));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RunOpenCode\Bundle\ExchangeRate\Model\Rate',
        ));
    }

    /**
     * Handle on post-set-data event.
     *
     * @param FormEvent $event
     */
    protected function onPostSetData(FormEvent $event)
    {
        /**
         * @var Rate $rate
         */
        $rate = $event->getData();

        if (
            (bool) $rate->getCurrencyCode()
            &&
            (bool) $rate->getRateType()
            &&
            (bool) $rate->getSourceName()
        ) {
            $event
                ->getForm()
                ->get('rate')
                ->setData(sprintf('%s|%s|%s', $rate->getCurrencyCode(), $rate->getRateType(), $rate->getSourceName()));
        }
    }

    /**
     * Handle on-submit event.
     *
     * @param FormEvent $event
     */
    protected function onSubmit(FormEvent $event)
    {
        /**
         * @var Rate $rate
         */
        $rate = $event->getData();

        $values = explode('|', $event->getForm()->get('rate')->getData());

        $rate
            ->setCurrencyCode($values[0])
            ->setRateType($values[1])
            ->setSourceName($values[2]);
    }
}
