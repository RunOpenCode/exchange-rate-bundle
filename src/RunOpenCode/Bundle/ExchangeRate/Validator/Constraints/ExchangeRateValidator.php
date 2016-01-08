<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Validator\Constraints;

use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\RatesConfigurationRegistryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ExchangeRateValidator
 *
 * Exchange rate validator.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Validator\Constraints
 */
class ExchangeRateValidator extends ConstraintValidator
{
    /**
     * @var RatesConfigurationRegistryInterface
     */
    protected $ratesConfiguration;

    public function __construct(RatesConfigurationRegistryInterface $ratesConfiguration)
    {
        $this->ratesConfiguration = $ratesConfiguration;
    }

    /**
     * {@inheritdoc}
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
