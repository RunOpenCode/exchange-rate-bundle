<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\Listener;

use RunOpenCode\Bundle\ExchangeRate\Event\FetchErrorEvent;
use RunOpenCode\Bundle\ExchangeRate\Event\FetchSuccessEvent;
use RunOpenCode\Bundle\ExchangeRate\Listener\FetchEventsEmailNotificationListener;
use RunOpenCode\ExchangeRate\Model\Rate;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class FetchEventsEmailNotificationListenerTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Listener
 */
class FetchEventsEmailNotificationListenerTest extends WebTestCase
{

    public function setUp()
    {
        self::bootKernel();
    }

    /**
     * @test
     */
    public function itSendsSuccessMailNotification()
    {
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailer
            ->expects($spy = $this->once())
            ->method('send');

        $listener = new FetchEventsEmailNotificationListener($this->getTwig(), $mailer, ['test@test.com']);

        $listener->onFetchSuccess(new FetchSuccessEvent([
            'some_source' => [
                new Rate('some_source', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                new Rate('some_source', 12, 'CHF', 'median', new \DateTime(), 'RSD'),
            ],
            'some_other_source' => [
                new Rate('some_other_source', 8, 'USD', 'median', new \DateTime(), 'RSD'),
            ],
        ], new \DateTime('now')));

        /**
         * @var \PHPUnit_Framework_MockObject_Invocation_Object $invocation
         */
        $invocation = $spy->getInvocations()[0];

        /**
         * @var \Swift_Message $message
         */
        $message = $invocation->parameters[0];

        $this->assertInstanceOf(\Swift_Message::class, $message);

        $this->assertEquals(['test@test.com' => null], $message->getTo());

        $crawler = new Crawler($message->getBody());

        $this->assertEquals(2, $crawler->filter('table')->count());

        $this->assertEquals(2, $crawler->filter('table')->first()->filter('tbody tr')->count());
        $this->assertEquals(1, $crawler->filter('table')->last()->filter('tbody tr')->count());
    }

    /**
     * @test
     */
    public function itSendsErrorMailNotification()
    {
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailer
            ->expects($spy = $this->once())
            ->method('send');

        $listener = new FetchEventsEmailNotificationListener($this->getTwig(), $mailer, ['test@test.com']);

        $listener->onFetchError(new FetchErrorEvent([
            'some_source' => new \Exception('Error for some_source.'),
            'some_other_source' => new \Exception('Error for some_other_source.'),
        ], new \DateTime('now')));

        /**
         * @var \PHPUnit_Framework_MockObject_Invocation_Object $invocation
         */
        $invocation = $spy->getInvocations()[0];

        /**
         * @var \Swift_Message $message
         */
        $message = $invocation->parameters[0];

        $this->assertInstanceOf(\Swift_Message::class, $message);

        $this->assertEquals(['test@test.com' => null], $message->getTo());

        $this->assertContains('Error for some_source.', $message->getBody());
        $this->assertContains('Error for some_other_source.', $message->getBody());
    }

    /**
     * @return \Twig_Environment
     */
    private function getTwig()
    {
        return self::$kernel->getContainer()->get('twig');
    }
}
