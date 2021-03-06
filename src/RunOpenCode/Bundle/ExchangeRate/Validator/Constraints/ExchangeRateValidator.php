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

use RunOpenCode\Bundle\ExchangeRate\Exception\InvalidArgumentException;
use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
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
        if (!$constraint instanceof ExchangeRate) {
            throw new InvalidArgumentException(sprintf('Expected instance of "%s", got "%s".', ExchangeRate::class, get_class($constraint)));
        }

        if (null === $rate) {
            return;
        }

        if (!$rate instanceof RateInterface) {
            throw new InvalidArgumentException(sprintf('Expected instance of "%s", got "%s".', RateInterface::class, get_class($rate)));
        }


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
                    $rateConfiguration->getSourceName() === $rate->getSourceName()
                ) {
                    return;
                }
            }
            /**
             * @var ExchangeRate $constraint
             */
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ rate_type }}', $rate->getRateType())
                ->setParameter('{{ currency_code }}', $rate->getCurrencyCode())
                ->setParameter('{{ source_name }}', $rate->getSourceName())
                ->setTranslationDomain('runopencode_exchange_rate')
                ->addViolation();
        }
    }
}
