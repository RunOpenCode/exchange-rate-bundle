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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
     * Get service
     *
     * @param string $id
     * @return object
     */
    private function get($id)
    {
        return self::$kernel->getContainer()->get($id);
    }
}
