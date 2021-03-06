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
 * Class DeleteControllerTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Controller
 */
class DeleteControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::setupRepository();
    }

    /**
     * @test
     */
    public function itDeniesAccessToConfirmDelete()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => 'foo',
        ]);

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_delete', [
            'source' => 'test_source',
            'rate_type' => 'median',
            'currency_code' => 'EUR',
            'date' => '2017-01-01'
        ]));

        $response = $client->getResponse();

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function itDeniesAccessToExecuteDelete()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => 'foo',
        ]);

        $this->get('security.csrf.token_manager')->on('isTokenValid', true);

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_execute_delete', [
            'source' => 'test_source',
            'rate_type' => 'median',
            'currency_code' => 'EUR',
            'date' => '2017-01-01'
        ]));

        $response = $client->getResponse();

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function itDeletesRate()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $this->assertTrue($this->get('runopencode.exchange_rate.repository')->has('test_source', 'EUR', new \DateTime('2017-01-01'), 'median'));

        $crawler = $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_delete', [
            'source' => 'test_source',
            'rate_type' => 'median',
            'currency_code' => 'EUR',
            'date' => '2017-01-01'
        ]));

        $link = $crawler->filter('a')->first()->link();

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $client->getContainer()->get('security.csrf.token_manager')->on('isTokenValid', true);

        $client->click($link);

        $this->assertFalse($this->get('runopencode.exchange_rate.repository')->has('test_source', 'EUR', new \DateTime('2017-01-01'), 'median'));
    }

    /**
     * @test
     */
    public function itHasInvalidCsrfToken()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $this->get('security.csrf.token_manager')->on('isTokenValid', false);

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_execute_delete', [
            'source' => 'test_source',
            'rate_type' => 'median',
            'currency_code' => 'EUR',
            'date' => '2017-01-01'
        ]));

        $response = $client->getResponse();

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function couldNotDeleteNonExistingRate()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $this->get('security.csrf.token_manager')->on('isTokenValid', true);

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_execute_delete', [
            'source' => 'test_source',
            'rate_type' => 'median',
            'currency_code' => 'BAM',
            'date' => '2017-01-01'
        ]));

        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function errorDelete()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $client->getContainer()->get('security.csrf.token_manager')->on('isTokenValid', true);
        $client->getContainer()->get('runopencode.exchange_rate.repository')->on('delete', new \Exception());

        $client->disableReboot();

        $crawler = $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_execute_delete', [
            'source' => 'test_source',
            'rate_type' => 'median',
            'currency_code' => 'USD',
            'date' => '2017-01-01'
        ]));

        $this->assertEquals(1, $crawler->filter('div.flash-error')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Could not delete exchange rate for unknown reason. Contact administrator.")')->count());
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
