<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class NewType
 *
 * New form.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Type
 */
class NewType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('rate', RateType::class, array(
            'mapped' => false,
            'placeholder' => '',
            'label' => 'exchange_rate.form.fields.rate',
            'translation_domain' => 'runopencode_exchange_rate'
        ));
    }
}
