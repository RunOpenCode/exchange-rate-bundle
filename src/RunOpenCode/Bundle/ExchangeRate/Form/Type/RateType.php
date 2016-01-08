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
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class RateType
 *
 * Rate choice type.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Type
 */
class RateType extends AbstractType
{
    protected $registry;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(RatesConfigurationRegistryInterface $registry, TranslatorInterface $translator)
    {
        $this->registry = $registry;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->getChoices(),
            'choice_translation_domain' => false
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
        $choices = array();

        /**
         * @var Configuration $configuration
         */
        foreach ($this->registry as $configuration) {
            $key = sprintf('%s|%s|%s', $configuration->getCurrencyCode(), $configuration->getRateType(), $configuration->getSource());
            $label = sprintf('%s, %s (%s)',
                $configuration->getCurrencyCode(),
                $this->translator->trans(sprintf('exchange_rate.rates.%s.%s.label', $configuration->getSource(), $configuration->getRateType()), array(), 'roc_exchange_rate'),
                $this->translator->trans(sprintf('exchange_rate.rates.%s.label', $configuration->getSource()), array(), 'roc_exchange_rate')
            );
            $choices[$key] = $label;
        }

        return $choices;
    }
}
