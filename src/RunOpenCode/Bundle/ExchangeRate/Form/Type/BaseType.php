<?php

namespace RunOpenCode\Bundle\ExchangeRate\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array());

        $builder->add('date', 'Symfony\Component\Form\Extension\Core\Type\DateType', array());

        $builder->add('currency_code', RegisteredCurrencyCodeType::class, array());

        $builder->add('rate_type', RegisteredRateType::class, array());

        $builder->add('source_name', RegisteredSourceType::class, array());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RunOpenCode\Bundle\ExchangeRate\Model\Rate',
        ));
    }
}
