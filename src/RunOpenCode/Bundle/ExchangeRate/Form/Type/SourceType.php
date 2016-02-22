<?php

namespace RunOpenCode\Bundle\ExchangeRate\Form\Type;

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

    public function __construct(array $defaults)
    {
        $this->defaults = array_merge(array(
            'choice_translation_domain' => 'roc_exchange_rate'
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
