<?php

namespace RunOpenCode\Bundle\ExchangeRate\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * {@inheritdoc}
 *
 * @Annotation
 */
class ExchangeRate extends Constraint
{
    public $message = 'Provided currency code and rate type is not valid in this context.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'run_open_code.exchange_rate.rate_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
