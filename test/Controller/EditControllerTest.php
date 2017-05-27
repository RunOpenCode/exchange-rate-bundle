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
use RunOpenCode\ExchangeRate\Model\Rate;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EditControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::clearRepository();

        self::$kernel->getContainer()->get('runopencode.exchange_rate.repository')->save([
            new Rate('test_source', 10, 'EUR', 'median', new \DateTime('2017-01-01'), 'RSD')
        ]);
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

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_edit', [
            'date' =>  '2017-01-01',
            'currency_code' => 'EUR',
            'rate_type' => 'median',
            'source'=>'test_source'
        ]));

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

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_edit', [
            'date' =>  '2017-01-01',
            'currency_code' => 'EUR',
            'rate_type' => 'median',
            'source'=>'test_source'
        ]));

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function thereIsNoRate()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_edit', [
            'date' =>  date('Y-m-d'),
            'currency_code' => 'CHF',
            'rate_type' => 'median',
            'source'=>'test_source'
        ]));

        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function itEditsRate()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'buzz',
            'PHP_AUTH_PW'   => 'buzz',
        ]);

        $this->assertTrue($this->get('runopencode.exchange_rate.repository')->has('test_source', 'EUR', new \DateTime('2017-01-01'), 'median'));

        $crawler = $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_edit', [
            'date' =>  '2017-01-01',
            'currency_code' => 'EUR',
            'rate_type' => 'median',
            'source'=>'test_source'
        ]));

        $form = $crawler->selectButton('form[submit]')->form();

        $form['form[date][day]'] = '1';
        $form['form[date][month]'] = '1';
        $form['form[date][year]'] = '2017';
        $form['form[rate]'] = 'test_source.median.EUR';
        $form['form[value]'] = '100';

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('div.flash-success')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("You have successfully modified exchange rate.")')->count());

        $this->assertEquals(100, $this->get('runopencode.exchange_rate.repository')->get('test_source', 'EUR', new \DateTime('2017-01-01'), 'median')->getValue());
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

        $this->assertTrue($this->get('runopencode.exchange_rate.repository')->has('test_source', 'EUR', new \DateTime('2017-01-01'), 'median'));

        $crawler = $crawler = $client->request('GET', $this->get('router')->generate('runopencode_exchange_rate_edit', [
            'date' =>  '2017-01-01',
            'currency_code' => 'EUR',
            'rate_type' => 'median',
            'source'=>'test_source'
        ]));

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

        $client->getContainer()->get('runopencode.exchange_rate.repository')->on('save', new \Exception());
        $client->getContainer()->get('security.csrf.token_manager')->on('isTokenValid', true);

        $crawler = $client->request('POST', $this->get('router')->generate('runopencode_exchange_rate_edit', [
            'date' =>  '2017-01-01',
            'currency_code' => 'EUR',
            'rate_type' => 'median',
            'source'=>'test_source',
        ]), [
            'form' => [
                'date' => [
                    'day' => '1',
                    'month' => '1',
                    'year' => '2017'
                ],
                'rate' => 'test_source.median.EUR',
                'value' => '100',
                '_token' => true
            ]
        ]);

        $this->assertEquals(1, $crawler->filter('div.flash-error')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Could not save exchange rate for unknown reason. Contact administrator.")')->count());
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
        if (null === self::$kernel || null === self::$kernel->getContainer()) {
            static::bootKernel();
        }

        $repository = self::$kernel->getContainer()->get('runopencode.exchange_rate.repository');
        $repository->delete($repository->all());
    }
}