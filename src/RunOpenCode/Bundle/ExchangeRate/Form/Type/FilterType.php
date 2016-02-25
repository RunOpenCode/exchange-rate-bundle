<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class FilterType
 *
 * Filter type.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Type
 */
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
            ->add('currencyCode', ForeignCurrencyCodeType::class, array(
                'placeholder' => '',
                'required' => false
            ))
            ->setMethod('GET')
        ;
    }
}
