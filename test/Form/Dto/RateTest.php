<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\Form\Dto;

use RunOpenCode\Bundle\ExchangeRate\Form\Dto\Rate;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class RateTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Form\Dto
 */
class RateTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();
        self::bootKernel();
    }

    /**
     * @test
     */
    public function validation()
    {
        $cases = [
            ['rate' => 'test_source.median.EUR', 'date' => new \DateTime('now'), 'value' => 10, 'baseCurrencyCode' => 'RSD', 'violationCount' => 0, 'violationMessages' => []],
            ['rate' => 'test_source.median.BAM', 'date' => new \DateTime('now'), 'value' => 10, 'baseCurrencyCode' => 'RSD', 'violationCount' => 1, 'violationMessages' => ['validator.rate.invalid']],
            ['rate' => null, 'date' => new \DateTime('now'), 'value' => 10, 'baseCurrencyCode' => 'RSD', 'violationCount' => 1, 'violationMessages' => ['This value should not be blank.']],
            ['rate' => 'test_source.median.EUR', 'date' => null, 'value' => 10, 'baseCurrencyCode' => 'RSD', 'violationCount' => 1, 'violationMessages' => ['This value should not be blank.']],
            ['rate' => 'test_source.median.EUR', 'date' => new \DateTime('now'), 'value' => null, 'baseCurrencyCode' => 'RSD', 'violationCount' => 1, 'violationMessages' => ['This value should not be blank.']],
            ['rate' => 'test_source.median.EUR', 'date' => new \DateTime('now'), 'value' => 'a string', 'baseCurrencyCode' => 'RSD', 'violationCount' => 1, 'violationMessages' => ['This value should be of type {{ type }}.']],
            ['rate' => 'test_source.median.EUR', 'date' => new \DateTime('now'), 'value' => 10, 'baseCurrencyCode' => null, 'violationCount' => 1, 'violationMessages' => ['This value should not be blank.']],
            ['rate' => 'test_source.median.EUR', 'date' => new \DateTime('now'), 'value' => 10, 'baseCurrencyCode' => 'EUR', 'violationCount' => 1, 'violationMessages' => ['validator.baseCurrency.invalid']],
        ];

        /**
         * @var \Symfony\Component\Validator\Validator\ValidatorInterface $validator
         */
        $validator = self::$kernel->getContainer()->get('validator');

        foreach ($cases as $input) {

            $dto = new Rate($input['rate'], $input['date'], $input['value'], $input['baseCurrencyCode']);

            /**
             * @var \Symfony\Component\Validator\ConstraintViolationListInterface $violations
             */
            $violations = $validator->validate($dto);

            $this->assertEquals($input['violationCount'], $violations->count());

            $violationMessages = [];

            /**
             * @var \Symfony\Component\Validator\ConstraintViolationInterface $violation
             */
            foreach ($violations as $violation) {
                $violationMessages[] = $violation->getMessageTemplate();
            }

            $this->assertEquals($input['violationMessages'], $violationMessages);
        }
    }

    /**
     * @test
     */
    public function coverage()
    {
        $rate = new Rate(null, null, null, 'RSD');

        $this->assertNull($rate->getCreatedAt());
        $this->assertNull($rate->getModifiedAt());

        $this->assertNull($rate->getRateType());
        $this->assertNull($rate->getSourceName());
        $this->assertNull($rate->getCurrencyCode());

        $this->assertEquals('RSD', $rate->getBaseCurrencyCode());
    }
}
