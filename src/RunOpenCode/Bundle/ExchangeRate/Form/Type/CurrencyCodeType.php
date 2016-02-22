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
class CurrencyCodeType extends AbstractType
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
