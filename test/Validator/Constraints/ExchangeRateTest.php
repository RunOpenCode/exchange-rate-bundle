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
use RunOpenCode\Bundle\ExchangeRate\Validator\Constraints\ExchangeRate;

/**
 * Class ExchangeRateTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Validator\Constraints
 */
class ExchangeRateTest extends TestCase
{
    /**
     * @test
     */
    public function constraintSettings()
    {
        $constraint = new ExchangeRate();

        $this->assertEquals(ExchangeRate::CLASS_CONSTRAINT, $constraint->getTargets());
        $this->assertEquals('runopencode.exchange_rate.rate_validator', $constraint->validatedBy());
    }
}
