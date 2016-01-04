<?php

namespace RunOpenCode\Bundle\ExchangeRate\Form\Type;

use RunOpenCode\Bundle\ExchangeRate\Model\Rate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array());

        $builder->add('date', 'Symfony\Component\Form\Extension\Core\Type\DateType', array());

        $builder->add('rate', RateType::class, array(
            'mapped' => false
        ));

        $builder->addEventListener(FormEvents::POST_SET_DATA, array($this, 'onPostSetData'));
        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RunOpenCode\Bundle\ExchangeRate\Model\Rate',
        ));
    }

    public function onPostSetData(FormEvent $event)
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

    public function onSubmit(FormEvent $event)
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
