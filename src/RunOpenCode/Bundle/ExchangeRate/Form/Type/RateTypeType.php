<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Form\Type;

use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\RatesConfigurationRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RateTypeType
 *
 * Rate type choices.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Type
 */
class RateTypeType extends AbstractType
{
    /**
     * @var array
     */
    protected $defaults;

    public function __construct(RatesConfigurationRegistryInterface $registry, array $defaults = [])
    {
        $choices = [];

        /**
         * @var Configuration $configuration
         */
        foreach ($registry as $configuration) {
            $rateType = $configuration->getRateType();
            $choices[$rateType] = $rateType;
        }

        $this->defaults = array_merge(array(
            'choice_translation_domain' => 'runopencode_exchange_rate',
            'choices' => $choices
        ), $defaults);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults($this->defaults);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
