<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use RunOpenCode\Bundle\ExchangeRate\Validator\Constraints\BaseCurrency;

/**
 * Class BaseCurrencyTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Validator\Constraints
 */
class BaseCurrencyTest extends TestCase
{
    /**
     * @test
     */
    public function constraintSettings()
    {
        $constraint = new BaseCurrency();

        $this->assertEquals(BaseCurrency::PROPERTY_CONSTRAINT, $constraint->getTargets());
        $this->assertEquals('runopencode.exchange_rate.base_currency_validator', $constraint->validatedBy());
    }
}