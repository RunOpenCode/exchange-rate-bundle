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

use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\RatesConfigurationRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CurrencyType
 *
 * Currency choice type.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Type
 */
class CurrencyType extends AbstractType
{
    /**
     * @var RatesConfigurationRegistryInterface
     */
    protected $registry;

    /**
     * @var string
     */
    protected $baseCurrency;

    public function __construct($baseCurrency, RatesConfigurationRegistryInterface $registry)
    {
        $this->baseCurrency = $baseCurrency;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->getChoices(),
            'choice_translation_domain' => false,
            'preferred_choices' => array($this->baseCurrency)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * Get choices for type.
     *
     * @return array
     */
    protected function getChoices()
    {
        $choices = array(
            $this->baseCurrency => $this->baseCurrency
        );

        /**
         * @var Configuration $configuration
         */
        foreach ($this->registry as $configuration) {
            $choices[$configuration->getCurrencyCode()] = $configuration->getCurrencyCode();
        }

        asort($choices);

        return $choices;
    }
}
