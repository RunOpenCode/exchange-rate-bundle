<?php

namespace RunOpenCode\Bundle\ExchangeRate\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

class NewType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('rate', RateType::class, array(
            'mapped' => false,
            'placeholder' => ''
        ));
    }
}
