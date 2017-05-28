<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\Controller;

use RunOpenCode\ExchangeRate\Model\Rate;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RestControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::setupRepository();
    }

    /**
     * @test
     */
    public function itHasRate()
    {
        $client = static::createClient();

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_rest_has', [
            'source' => 'test_source',
            'currency_code' => 'EUR',
            'date' => '2017-01-01',
            'rate_type' => 'median'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($data['result']);
    }

    /**
     * Get service
     *
     * @param string $id
     * @return object
     */
    private function get($id)
    {
        return self::$kernel->getContainer()->get($id);
    }

    /**
     * Clears repository
     */
    private static function setupRepository()
    {
        if (null === self::$kernel || null === self::$kernel->getContainer()) {
            static::bootKernel();
        }

        $repository = self::$kernel->getContainer()->get('runopencode.exchange_rate.repository');
        $repository->delete($repository->all());

        $repository->save([
            new Rate('test_source', 10, 'EUR', 'median', new \DateTime('2017-01-01'), 'RSD'),
            new Rate('test_source', 12, 'CHF', 'median', new \DateTime('2017-01-01'), 'RSD'),
            new Rate('test_source', 8, 'USD', 'median', new \DateTime('2017-01-01'), 'RSD'),
        ]);
    }
}