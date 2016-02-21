<?php

namespace RunOpenCode\Bundle\ExchangeRate\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('onDate', DateType::class, array(
                'required' => false
            ))
            ->add('sourceName', SourceType::class, array(
                'placeholder' => '',
                'required' => false
            ))
            ->add('rateType', RateTypeType::class, array(
                'placeholder' => '',
                'required' => false
            ))
            ->add('currencyCode', CurrencyCodeType::class, array(
                'placeholder' => '',
                'required' => false
            ))
            ->setMethod('GET')
        ;
    }
}
