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
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class BaseCurrencyValidator
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Validator\Constraints
 */
class BaseCurrencyValidator extends ConstraintValidator
{
    /**
     * @var string
     */
    private $baseCurrencyCode;

    public function __construct($baseCurrencyCode)
    {
        $this->baseCurrencyCode = $baseCurrencyCode;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof BaseCurrency) {
            throw new InvalidArgumentException(sprintf('Expected instance of "%s", got "%s".', BaseCurrency::class, get_class($constraint)));
        }

        if (null === $value) {
            return;
        }

        if ($this->baseCurrencyCode !== $value) {

            /**
             * @var BaseCurrency $constraint
             */
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ base_currency_code }}', $this->baseCurrencyCode)
                ->setParameter('{{ value }}', $value)
                ->setTranslationDomain('runopencode_exchange_rate')
                ->addViolation();
        }
    }
}
