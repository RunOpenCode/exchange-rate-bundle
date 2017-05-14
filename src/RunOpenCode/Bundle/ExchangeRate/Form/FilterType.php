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

use RunOpenCode\Bundle\ExchangeRate\Form\Type\ForeignCurrencyCodeType;
use RunOpenCode\Bundle\ExchangeRate\Form\Type\RateTypeType;
use RunOpenCode\Bundle\ExchangeRate\Form\Type\SourceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FilterType
 *
 * Filter type.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Type
 */
class FilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('onDate', DateType::class, [
                'label' => 'filter.fields.onDate.label',
                'placeholder' => 'filter.fields.onDate.placeholder',
                'required' => false,
                'translation_domain' => 'runopencode_exchange_rate',
            ])
            ->add('sourceName', SourceType::class, [
                'label' => 'filter.fields.sourceName.label',
                'placeholder' => 'filter.fields.sourceName.placeholder',
                'required' => false,
                'translation_domain' => 'runopencode_exchange_rate',
            ])
            ->add('rateType', RateTypeType::class, [
                'label' => 'filter.fields.rateType.label',
                'placeholder' => 'filter.fields.rateType.placeholder',
                'required' => false,
                'translation_domain' => 'runopencode_exchange_rate',
            ])
            ->add('currencyCode', ForeignCurrencyCodeType::class, [
                'label' => 'filter.fields.currencyCode.label',
                'placeholder' => 'filter.fields.currencyCode.placeholder',
                'required' => false,
                'translation_domain' => 'runopencode_exchange_rate',
            ])
            ->add('submit', ButtonType::class, [
                'label' => 'filter.submit.label',
                'translation_domain' => 'runopencode_exchange_rate',
            ])
            ->setMethod('GET')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('csrf_protection', false);
    }
}
