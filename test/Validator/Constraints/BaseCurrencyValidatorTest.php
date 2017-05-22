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
use RunOpenCode\Bundle\ExchangeRate\Validator\Constraints\BaseCurrencyValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * Class BaseCurrencyValidatorTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Validator\Constraints
 */
class BaseCurrencyValidatorTest extends TestCase
{
    /**
     * @test
     *
     * @expectedException \RunOpenCode\Bundle\ExchangeRate\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected instance of "RunOpenCode\Bundle\ExchangeRate\Validator\Constraints\BaseCurrency", got "Constraint".
     */
    public function itExpectsBaseCurrencyConstraintClass()
    {
        $validator = new BaseCurrencyValidator('RSD');
        $validator->validate(null, $this->getMockBuilder(Constraint::class)->setMockClassName('Constraint')->getMock());
    }

    /**
     * @test
     */
    public function validationPass()
    {
        $validator = new BaseCurrencyValidator('RSD');

        $validationContext = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        $validationContext
            ->expects($this->never())
            ->method('buildViolation')
            ->willReturn($validationContext);

        $validator->initialize($validationContext);

        $validator->validate('RSD', new BaseCurrency());
    }

    /**
     * @test
     */
    public function validationPassOnNull()
    {
        $validator = new BaseCurrencyValidator('RSD');

        $validationContext = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        $validationContext
            ->expects($this->never())
            ->method('buildViolation')
            ->willReturn($validationContext);

        $validator->initialize($validationContext);

        $validator->validate(null, new BaseCurrency());
    }

    /**
     * @test
     */
    public function validationFails()
    {
        $validator = new BaseCurrencyValidator('RSD');
        $constraint = new BaseCurrency();

        $validationContext = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $validationBuilder = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();

        $validationContext
            ->expects($this->exactly(1))
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($validationBuilder);

        $validationBuilder
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturn($validationBuilder);

        $validationBuilder
            ->expects($this->exactly(1))
            ->method('setTranslationDomain')
            ->willReturn($validationBuilder);

        $validationBuilder
            ->expects($this->exactly(1))
            ->method('addViolation')
            ->willReturn($validationBuilder);


        $validator->initialize($validationContext);

        $validator->validate('CHF', $constraint);
    }
}
