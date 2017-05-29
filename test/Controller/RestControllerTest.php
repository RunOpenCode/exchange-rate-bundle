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

/**
 * Class RestControllerTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Controller
 */
class RestControllerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
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
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertTrue($data['result']);
    }

    /**
     * @test
     */
    public function itDoesNotHaveRate()
    {
        $client = static::createClient();

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_rest_has', [
            'source' => 'test_source',
            'currency_code' => 'EUR',
            'date' => '2017-01-02',
            'rate_type' => 'median'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse($data['result']);
    }

    /**
     * @test
     */
    public function itErrorsOutOnHas()
    {
        $client = static::createClient();

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_rest_has', [
            'source' => 'test_source',
            'currency_code' => 'not-a-rate',
            'date' => '2017-01-01',
            'rate_type' => 'median'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertTrue($data['error']);
    }

    /**
     * @test
     */
    public function itGetsRate()
    {
        $client = static::createClient();

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_rest_get', [
            'source' => 'test_source',
            'currency_code' => 'EUR',
            'date' => '2017-01-01',
            'rate_type' => 'median'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse($data['error']);
        $this->assertEquals(10, $data['result']['value']);
    }

    /**
     * @test
     */
    public function itCanNotGetRate()
    {
        $client = static::createClient();

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_rest_get', [
            'source' => 'test_source',
            'currency_code' => 'EUR',
            'date' => '2017-01-02',
            'rate_type' => 'median'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertTrue($data['error']);
        $this->assertEquals('Could not fetch rate for rate currency code "EUR" and rate type "median" on date "2017-01-02".', $data['message']);
    }

    /**
     * @test
     */
    public function itGetsLatest()
    {
        $client = static::createClient();

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_rest_latest', [
            'source' => 'test_source',
            'currency_code' => 'EUR',
            'date' => '2017-01-02',
            'rate_type' => 'median'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse($data['error']);
        $this->assertEquals(10, $data['result']['value']);
        $this->assertEquals('2017-01-01', $data['result']['date']);
    }

    /**
     * @test
     */
    public function itCanNotGetLatestRate()
    {
        $client = static::createClient();

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_rest_latest', [
            'source' => 'test_source',
            'currency_code' => 'BAM',
            'date' => '2017-01-02',
            'rate_type' => 'median'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertTrue($data['error']);
        $this->assertEquals('Could not fetch latest rate for rate currency code "BAM" and rate type "median" from source "test_source".', $data['message']);
    }

    /**
     * @test
     */
    public function itGetsTodaysRate()
    {
        $client = static::createClient();

        $repository = $this->get('runopencode.exchange_rate.repository');

        $repository->save([$todaysRate = new Rate('test_source', 100, 'EUR', 'median', new \DateTime('now'), 'RSD')]);

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_rest_today', [
            'source' => 'test_source',
            'currency_code' => 'EUR',
            'rate_type' => 'median'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse($data['error']);
        $this->assertEquals(100, $data['result']['value']);
        $this->assertEquals(date('Y-m-d'), $data['result']['date']);

        $repository->delete([$todaysRate]);
    }

    /**
     * @test
     */
    public function itCanNotGetTodaysRate()
    {
        $client = static::createClient();

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_rest_today', [
            'source' => 'test_source',
            'currency_code' => 'EUR',
            'rate_type' => 'median'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertTrue($data['error']);
        $this->assertEquals(sprintf('Rate for currency code "EUR" of type "median" from source "test_source" is not available for today "%s".', date('Y-m-d')), $data['message']);
    }

    /**
     * @test
     */
    public function itGetsHistorical()
    {
        $client = static::createClient();

        $repository = $this->get('runopencode.exchange_rate.repository');

        $repository->save([$historicalRate = new Rate('test_source', 100, 'EUR', 'median', new \DateTime('2017-01-13'), 'RSD')]);

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_rest_historical', [
            'source' => 'test_source',
            'currency_code' => 'EUR',
            'date' => '2017-01-14',
            'rate_type' => 'median'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse($data['error']);
        $this->assertEquals(100, $data['result']['value']);
        $this->assertEquals('2017-01-13', $data['result']['date']);

        $repository->delete([$historicalRate]);
    }

    /**
     * @test
     */
    public function itCanNotGetHistoricalRate()
    {
        $client = static::createClient();

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_rest_historical', [
            'source' => 'test_source',
            'currency_code' => 'EUR',
            'date' => '2017-01-14',
            'rate_type' => 'median'
        ]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertTrue($data['error']);
        $this->assertEquals('Rate for currency code "EUR" of type "median" from source "test_source" is not available for historical date "2017-01-13".', $data['message']);
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