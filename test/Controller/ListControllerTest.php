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
 * Class ListControllerTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Controller
 */
class ListControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function itDeniesAccess()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW'   => 'foo',
        ]);

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_list'));

        $response = $client->getResponse();

        $this->assertEquals(403, $response->getStatusCode());

    }

    /**
     * @test
     */
    public function itGrantsAccess()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'bar',
            'PHP_AUTH_PW'   => 'bar',
        ]);

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_list'));

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function itListsAndFilters()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'bar',
            'PHP_AUTH_PW'   => 'bar',
        ]);

        $this->clearRepository();

        $crawler = $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_list'));

        $this->assertEquals(1, $crawler->filter('tbody tr')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("There are no rates to display.")')->count());

        $this->get('runopencode.exchange_rate.repository')->save([
            new Rate('test_source', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
            new Rate('test_source', 12, 'CHF', 'median', new \DateTime(), 'RSD'),
            new Rate('test_source', 8, 'USD', 'median', new \DateTime(), 'RSD'),
        ]);

        $crawler = $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_list'));

        $this->assertEquals(3, $crawler->filter('tbody tr')->count());

        $form = $crawler->selectButton('filter[submit]')->form();

        $form['filter[currencyCode]'] = 'EUR';

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('tbody tr')->count());
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
    private function clearRepository()
    {
        $repository = $this->get('runopencode.exchange_rate.repository');
        $repository->delete($repository->all());
    }
}
