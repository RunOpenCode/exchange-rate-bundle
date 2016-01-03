<?php

namespace RunOpenCode\Bundle\ExchangeRate\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

class NewType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('currency_code', RegisteredCurrencyCodeType::class, array('placeholder' => ''));

        $builder->add('rate_type', RegisteredRateType::class, array('placeholder' => ''));

        $builder->add('source_name', RegisteredSourceType::class, array('placeholder' => ''));
    }
}
