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

use RunOpenCode\ExchangeRate\Contract\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::clearRepository();
    }

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
    public function formIsInvalid()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $crawler = $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_create'));

        $form = $crawler->selectButton('form[submit]')->form();

        $form['form[date][day]'] = '1';
        $form['form[date][month]'] = '1';
        $form['form[date][year]'] = '2017';
        $form['form[rate]'] = 'test_source.median.CHF';
        $form['form[value]'] = '';

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('div.flash-error')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Form has error.")')->count());
    }

    /**
     * @test
     */
    public function itDoesNotAllowsCreatingExistingRate()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $this->assertTrue($this->get('runopencode.exchange_rate.repository')->has('test_source', 'EUR', new \DateTime('2017-01-01'), 'median'));

        $crawler = $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_create'));

        $form = $crawler->selectButton('form[submit]')->form();

        $form['form[date][day]'] = '1';
        $form['form[date][month]'] = '1';
        $form['form[date][year]'] = '2017';
        $form['form[rate]'] = 'test_source.median.EUR';
        $form['form[value]'] = '';

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('div.flash-error')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Form has error.")')->count());
    }

    /**
     * @test
     */
    public function couldNotSaveRate()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        self::$kernel->getContainer()->set('runopencode.exchange_rate.repository', $mock = $this->getMockBuilder(RepositoryInterface::class)->getMock());
        $mock
            ->method('save')
            ->willThrowException(new \Exception());

        $crawler = $client->request('POST', $this->get('router')->generate('runopencode_exchange_rate_create'), [
            'form' => [
                'date' => [
                    'day' => '1',
                    'month' => '1',
                    'year' => '2017'
                ],
                'rate' => 'test_source.median.EUR',
                'value' => '100',
            ]
        ]);


        $this->assertEquals(1, $crawler->filter('div.flash-error')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Could not create new exchange rate for unknown reason. Contact administrator.")')->count());
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
    private static function clearRepository()
    {
        if (null === self::$kernel) {
            static::bootKernel();
        }

        $repository = self::$kernel->getContainer()->get('runopencode.exchange_rate.repository');
        $repository->delete($repository->all());
    }
}
