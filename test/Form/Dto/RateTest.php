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
            ['rate' => 'test_source.median.EUR', 'date' => new \DateTime('now'), 'value' => 10, 'baseCurrencyCode' => 'RSD', 'violationCount' => 0, 'violationMessages' => []]
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
                $violationMessages[] = $violation->getMessage();
            }

            $this->assertEquals($input['violationMessages'], $violationMessages);
        }
    }
}
