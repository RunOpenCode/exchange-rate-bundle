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
 * {@inheritdoc}
 *
 * @Annotation
 */
class ExchangeRate extends Constraint
{
    public $message = 'runopencode.exchange_rate.rate_validator.invalid';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'runopencode.exchange_rate.rate_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
