<?php

namespace RunOpenCode\Bundle\ExchangeRate\Validator\Constraints;

use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Contract\RatesConfigurationRegistryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ExchangeRateValidator extends ConstraintValidator
{
    protected $ratesConfiguration;

    public function __construct(RatesConfigurationRegistryInterface $ratesConfiguration)
    {
        $this->ratesConfiguration = $ratesConfiguration;
    }

    /**
     * Validate rate.
     *
     * @param RateInterface $rate
     * @param Constraint $constraint
     */
    public function validate($rate, Constraint $constraint)
    {
        if ($rate->getCurrencyCode() && $rate->getRateType()) {
            /**
             * @var Configuration $rateConfiguration
             */
            foreach ($this->ratesConfiguration as $rateConfiguration) {

                if (
                    $rateConfiguration->getRateType() === $rate->getRateType()
                    &&
                    $rateConfiguration->getCurrencyCode() === $rate->getCurrencyCode()
                    &&
                    $rateConfiguration->getSource() === $rate->getSourceName()
                ) {
                    return;
                }
            }
            /**
             * @var ExchangeRate $constraint
             */
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
