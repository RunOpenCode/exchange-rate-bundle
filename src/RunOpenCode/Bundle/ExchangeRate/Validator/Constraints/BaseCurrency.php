<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class BaseCurrency
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Validator\Constraints
 *
 * {@inheritdoc}
 *
 * @Annotation
 */
class BaseCurrency extends Constraint
{
    public $message = 'validator.baseCurrency.invalid';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'runopencode.exchange_rate.base_currency_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
