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

use RunOpenCode\Bundle\ExchangeRate\Form\Dto\Rate as DtoRate;
use RunOpenCode\Bundle\ExchangeRate\Form\Type\RateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rate', RateType::class, array(
                'label' => 'form.fields.rate.label',
                'required' => true,
            ))
            ->add('date', DateType::class, array(
                'label' => 'form.fields.date.label',
                'translation_domain' => 'runopencode_exchange_rate',
                'required' => true,
            ))
            ->add('value', NumberType::class, array(
                'label' => 'form.fields.date.label',
                'translation_domain' => 'runopencode_exchange_rate',
                'required' => true,
            ))
            ->add('submit', ButtonType::class, [
                'label' => 'form.submit.label',
                'translation_domain' => 'runopencode_exchange_rate',
            ]);
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DtoRate::class,
            'intention' => 'runopencode_exchange_rate_bundle_form'
        ));
    }
}
