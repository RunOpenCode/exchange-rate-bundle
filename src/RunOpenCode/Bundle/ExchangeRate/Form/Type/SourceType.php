<?php

namespace RunOpenCode\Bundle\ExchangeRate\Form\Type;

use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\RatesConfigurationRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SourceType
 *
 * Source choices type.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Type
 */
class SourceType extends AbstractType
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
            $sourceName = $configuration->getSourceName();
            $choices[$sourceName] = $sourceName;
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
