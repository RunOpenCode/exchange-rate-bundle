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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RateType
 *
 * Rate choice type - which can be used for CRUD operations only.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Type
 */
class RateType extends AbstractType
{
    /**
     * @var array
     */
    protected $defaults;

    public function __construct(RatesConfigurationRegistryInterface $registry, array $defaults)
    {
        $choices = [];

        /**
         * @var Configuration $configuration
         */
        foreach ($registry as $configuration) {
            $choice = sprintf('%s.%s.%s', $configuration->getSourceName(), $configuration->getRateType(), $configuration->getCurrencyCode());
            $choices[$choice] = $choice;
        }

        $this->defaults = array_merge(array(
            'choice_translation_domain' => 'roc_exchange_rate',
            'choices' => $choices
        ), $defaults);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults($this->defaults);
    }
}
