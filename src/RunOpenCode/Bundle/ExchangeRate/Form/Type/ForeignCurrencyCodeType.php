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
 * Class CurrencyType
 *
 * Foreign currency choice type.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Type
 */
class ForeignCurrencyCodeType extends AbstractType
{
    /**
     * @var array
     */
    protected $defaults;

    public function __construct(RatesConfigurationRegistryInterface $registry, array $defaults = [])
    {
        $currencyCodes = [];

        /**
         * @var Configuration $configuration
         */
        foreach ($registry as $configuration) {
            $currencyCode = $configuration->getCurrencyCode();
            $currencyCodes[$currencyCode] = $currencyCode;
        }

        asort($currencyCodes);

        $this->defaults = array_merge(array(
            'choice_translation_domain' => false,
            'choices' => $currencyCodes
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

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
