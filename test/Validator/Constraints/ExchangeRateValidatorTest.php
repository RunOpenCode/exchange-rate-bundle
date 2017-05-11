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
use RunOpenCode\Bundle\ExchangeRate\Validator\Constraints\ExchangeRateValidator;
use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\RatesConfigurationRegistryInterface;
use RunOpenCode\ExchangeRate\Model\Rate;
use RunOpenCode\ExchangeRate\Registry\RatesConfigurationRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * Class ExchangeRateValidatorTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Validator\Constraints
 */
class ExchangeRateValidatorTest extends TestCase
{
    /**
     * @test
     *
     * @expectedException \RunOpenCode\Bundle\ExchangeRate\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected instance of "RunOpenCode\Bundle\ExchangeRate\Validator\Constraints\ExchangeRate", got "Constraint".
     */
    public function itExpectsExchangeRateConstraintClass()
    {
        $validator = new ExchangeRateValidator($this->getMockBuilder(RatesConfigurationRegistryInterface::class)->getMock());
        $validator->validate(null, $this->getMockBuilder(Constraint::class)->setMockClassName('Constraint')->getMock());
    }

    /**
     * @test
     *
     * @expectedException \RunOpenCode\Bundle\ExchangeRate\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected instance of "RunOpenCode\ExchangeRate\Contract\RateInterface", got "RunOpenCode\Bundle\ExchangeRate\Tests\Validator\Constraints\ExchangeRateValidatorTest".
     */
    public function itExpectsToValidateRate()
    {
        $validator = new ExchangeRateValidator($this->getMockBuilder(RatesConfigurationRegistryInterface::class)->getMock());
        $validator->validate($this, new ExchangeRate());
    }

    /**
     * @test
     */
    public function validationPass()
    {
        $registry = new RatesConfigurationRegistry([
            new Configuration('EUR', 'median', 'dummy_source')
        ]);

        $validator = new ExchangeRateValidator($registry);

        $validationContext = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        $validationContext
            ->expects($this->never())
            ->method('buildViolation')
            ->willReturn($validationContext);

        $validator->initialize($validationContext);

        $validator->validate(new Rate(
            'dummy_source',
            10,
            'EUR',
            'median',
            new \DateTime('now'),
            'RSD'
        ), new ExchangeRate());
    }

    /**
     * @test
     */
    public function validationPassOnNull()
    {
        $registry = new RatesConfigurationRegistry([
            new Configuration('EUR', 'median', 'dummy_source')
        ]);

        $validator = new ExchangeRateValidator($registry);

        $validationContext = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        $validationContext
            ->expects($this->never())
            ->method('buildViolation')
            ->willReturn($validationContext);

        $validator->initialize($validationContext);

        $validator->validate(null, new ExchangeRate());
    }

    /**
     * @test
     */
    public function validationFails()
    {
        $registry = new RatesConfigurationRegistry([
            new Configuration('CHF', 'median', 'dummy_source')
        ]);

        $validator = new ExchangeRateValidator($registry);
        $constraint = new ExchangeRate();

        $validationContext = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $validationBuilder = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();

        $validationContext
            ->expects($this->exactly(1))
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($validationBuilder);

        $validationBuilder
            ->expects($this->exactly(1))
            ->method('addViolation')
            ->willReturn(null);

        $validator->initialize($validationContext);

        $validator->validate(new Rate(
            'dummy_source',
            10,
            'EUR',
            'median',
            new \DateTime('now'),
            'RSD'
        ), $constraint);
    }
}
