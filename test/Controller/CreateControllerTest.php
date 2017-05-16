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

class CreateControllerTest extends WebTestCase
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

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_create'));

        $response = $client->getResponse();

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function itGrantsAccess()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_create'));

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function itCreatesNewRate()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $this->clearRepository();

        $this->assertFalse($this->get('runopencode.exchange_rate.repository')->has('test_source', 'EUR', new \DateTime('2017-01-01'), 'median'));

        $crawler = $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_create'));

        $form = $crawler->selectButton('form[submit]')->form();

        $form['form[date][day]'] = '1';
        $form['form[date][month]'] = '1';
        $form['form[date][year]'] = '2017';
        $form['form[rate]'] = 'test_source.median.EUR';
        $form['form[value]'] = '100';

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('div.flash-success')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("You have successfully created new exchange rate.")')->count());

        $this->assertTrue($this->get('runopencode.exchange_rate.repository')->has('test_source', 'EUR', new \DateTime('2017-01-01'), 'median'));
    }

    /**
     * @test
     */
    public function formHasErrors()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $crawler = $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_create'));

        $form = $crawler->selectButton('form[submit]')->form();

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('div.flash-error')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Form has error.")')->count());
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
