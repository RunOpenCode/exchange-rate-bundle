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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class RateType
 *
 * Rate choice type - which can be used for CRUD operations only.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Type
 */
class RateType extends ChoiceType
{
    /**
     * @var RatesConfigurationRegistryInterface
     */
    protected $registry;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $defaults;

    public function __construct(RatesConfigurationRegistryInterface $registry, TranslatorInterface $translator, array $defaults)
    {
        parent::__construct(null);

        $this->registry = $registry;
        $this->translator = $translator;

        $this->defaults = array_merge(array(
            'choice_translation_domain' => 'roc_exchange_rate',
            'label_format'=> '{{currency-code}}, {{rate-type}} ({{source}})',
            'choices_as_values' => true
        ), $defaults);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults($this->defaults)
            ->setNormalizer('choices', \Closure::bind(function(Options $options, $choices) {
                return $this->getChoices($options);
            }, $this));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($defaults['choices']) || isset($defaults['choice_loader'])) {
            throw new \LogicException('You can not provide your own choice list for this type.');
        }

        parent::buildForm($builder, $options);
    }

    /**
     * Get choices for type.
     *
     * @return array
     */
    protected function getChoices(Options $options)
    {
        $choices = array();

        /**
         * @var Configuration $configuration
         */
        foreach ($this->registry as $configuration) {
            $key = sprintf('%s|%s|%s', $configuration->getCurrencyCode(), $configuration->getRateType(), $configuration->getSourceName());

            $label = str_replace(array('{{currency-code}}', '{{rate-type}}', '{{source}}'), array(
                $configuration->getCurrencyCode(),
                $this->translator->trans(sprintf('exchange_rate.rate_type.%s.%s', $configuration->getSourceName(), $configuration->getRateType()), array(), $options['choice_translation_domain']),
                $this->translator->trans(sprintf('exchange_rate.source.%s', $configuration->getSourceName()), array(), $options['choice_translation_domain'])
            ), $options['label_format']);

            $choices[$label] = $key;
        }

        return $choices;
    }
}
