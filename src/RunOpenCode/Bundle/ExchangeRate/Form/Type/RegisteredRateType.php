<?php

namespace RunOpenCode\Bundle\ExchangeRate\Form\Type;

use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\RatesConfigurationRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisteredRateType extends AbstractType
{
    protected $registry;

    public function __construct(RatesConfigurationRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->getChoices()
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    protected function getChoices()
    {
        $choices = array();

        /**
         * @var Configuration $configuration
         */
        foreach ($this->registry as $configuration) {
            $choices[$configuration->getRateType()] = $configuration->getRateType();
        }

        return $choices;
    }
}
