<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use RunOpenCode\Bundle\ExchangeRate\Command\FetchCommand;
use RunOpenCode\Bundle\ExchangeRate\Event\FetchErrorEvent;
use RunOpenCode\Bundle\ExchangeRate\Event\FetchEvents;
use RunOpenCode\Bundle\ExchangeRate\Event\FetchSuccessEvent;
use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\ManagerInterface;
use RunOpenCode\ExchangeRate\Contract\SourceInterface;
use RunOpenCode\ExchangeRate\Contract\SourcesRegistryInterface;
use RunOpenCode\ExchangeRate\Model\Rate;
use RunOpenCode\ExchangeRate\Registry\RatesConfigurationRegistry;
use RunOpenCode\ExchangeRate\Registry\SourcesRegistry;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FetchCommandTest extends TestCase
{
    /**
     * @test
     */
    public function itSuccessfulyExecutesWithNoParameters()
    {
        $manager = $this->getManager();

        $manager
            ->method('fetch')
            ->will($this->onConsecutiveCalls(
                [
                    new Rate('source_1', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                ],
                [
                    new Rate('source_2', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                ]
            ));

        $application = $this->buildApplication(null, $manager);

        $returnValue = $application->doRun(new ArrayInput([]), new NullOutput());

        $this->assertEquals(0, $returnValue);
    }

    /**
     * @test
     */
    public function itSanitizesDateProperly()
    {
        $manager = $this->getManager();

        $manager
            ->method('fetch')
            ->will($this->onConsecutiveCalls(
                [
                    new Rate('source_1', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                ],
                [
                    new Rate('source_2', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                ],
                [
                    new Rate('source_1', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                ],
                [
                    new Rate('source_2', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                ]
            ));

        $application = $this->buildApplication(null, $manager);

        $inputDate = [
            '2017-01-21',
            (new \DateTime())->format(\DateTime::ATOM)
        ];

        foreach ($inputDate as $date) {

            $returnValue = $application->find('runopencode:exchange-rate:fetch')->run(new ArrayInput([
                '--date' => $date,
            ]), new NullOutput());

            $this->assertEquals(0, $returnValue);
        }

    }

    /**
     * @test
     * @expectedException \RunOpenCode\Bundle\ExchangeRate\Exception\InvalidArgumentException
     */
    public function whenDateSanitazionIsNotPossibleItThrowsException()
    {
        $application = $this->buildApplication();

        $application->find('runopencode:exchange-rate:fetch')->run(new ArrayInput([
            '--date' => 'unknownstring',
        ]), new NullOutput());
    }

    /**
     * @test
     */
    public function itUsesSingleSourceOnly()
    {
        $manager = $this->getManager();

        $manager
            ->method('fetch')
            ->will($this->onConsecutiveCalls(
                [
                    new Rate('source_1', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                ]
            ));

        $application = $this->buildApplication(null, $manager);

        $returnValue = $application->find('runopencode:exchange-rate:fetch')->run(new ArrayInput([
            '--source' => 'source_1',
        ]), new NullOutput());

        $this->assertEquals(0, $returnValue);
    }

    /**
     * @test
     */
    public function itSanitizesSeveralSources()
    {
        $manager = $this->getManager();

        $manager
            ->method('fetch')
            ->will($this->onConsecutiveCalls(
                [
                    new Rate('source_1', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                ],
                [
                    new Rate('source_2', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                ]
            ));

        $application = $this->buildApplication(null, $manager);

        $returnValue = $application->find('runopencode:exchange-rate:fetch')->run(new ArrayInput([
            '--source' => 'source_1, source_2',
        ]), new NullOutput());

        $this->assertEquals(0, $returnValue);
    }

    /**
     * @test
     * @expectedException \RunOpenCode\Bundle\ExchangeRate\Exception\InvalidArgumentException
     */
    public function onMissingSourceItThrowsException()
    {
        $application = $this->buildApplication();

        $application->find('runopencode:exchange-rate:fetch')->run(new ArrayInput([
            '--source' => 'missing_source',
        ]), new NullOutput());
    }

    /**
     * @test
     */
    public function whenRatesCouldNotBeFetchedItErrorsOut()
    {
        $manager = $this->getManager();

        $manager
            ->method('fetch')
            ->willReturn([]);

        $application = $this->buildApplication(null, $manager);

        $returnValue = $application->find('runopencode:exchange-rate:fetch')->run(new ArrayInput([
            '--source' => 'source_1',
        ]), new NullOutput());

        $this->assertEquals(-1, $returnValue);
    }

    /**
     * @test
     */
    public function itNotifiesAboutSuccessAndErrors()
    {
        $manager = $this->getManager();

        $ed = $this->getEventDispatcher();
        $ed
            ->expects($spy = $this->any())
            ->method('dispatch');

        $manager
            ->method('fetch')
            ->will($this->onConsecutiveCalls(
                [
                    $rate = new Rate('source_1', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                ],
                [

                ]
            ));

        $application = $this->buildApplication($ed, $manager);

        $returnValue = $application->find('runopencode:exchange-rate:fetch')->run(new ArrayInput([
            '--source' => 'source_1, source_2',
        ]), new NullOutput());

        $invocations = $spy->getInvocations();
        $this->assertEquals(2, count($invocations));

        $this->assertEquals(FetchEvents::SUCCESS, $invocations[0]->parameters[0]);
        $this->assertEquals(FetchSuccessEvent::class, get_class($invocations[0]->parameters[1]));
        $this->assertEquals([$rate], $invocations[0]->parameters[1]->getRates()['source_1']);

        $this->assertEquals(FetchEvents::ERROR, $invocations[1]->parameters[0]);
        $this->assertEquals(FetchErrorEvent::class, get_class($invocations[1]->parameters[1]));
        $this->assertInstanceOf(\Exception::class, $invocations[1]->parameters[1]->getErrors()['source_2']);

        $this->assertEquals(-1, $returnValue);
    }

    /**
     * @test
     */
    public function itDoesNotNotifiesWhenSilenced()
    {
        $manager = $this->getManager();

        $ed = $this->getEventDispatcher();
        $ed
            ->expects($spy = $this->any())
            ->method('dispatch');

        $manager
            ->method('fetch')
            ->will($this->onConsecutiveCalls(
                [
                    $rate = new Rate('source_1', 10, 'EUR', 'median', new \DateTime(), 'RSD'),
                ],
                [

                ]
            ));

        $application = $this->buildApplication($ed, $manager);

        $returnValue = $application->find('runopencode:exchange-rate:fetch')->run(new ArrayInput([
            '--source' => 'source_1, source_2',
            '--silent' => true
        ]), new NullOutput());

        $invocations = $spy->getInvocations();
        $this->assertEquals(0, count($invocations));
    }

    /**
     * @param null|EventDispatcherInterface $sources
     * @param null|ManagerInterface $manager
     * @param null|SourcesRegistryInterface $sources
     *
     * @return Application
     */
    private function buildApplication($eventDispatcher = null, $manager = null, $sources = null)
    {
        $application = new Application();

        $command = new FetchCommand(
            $eventDispatcher ?? $this->getEventDispatcher(),
            $manager ?? $this->getManager(),
            $sources ?? $this->getSources()
        );

        $application->add($command);
        $application->setDefaultCommand($command->getName());

        return $application;
    }

    /**
     * @return RatesConfigurationRegistry
     */
    private function getRates()
    {
        $rates = new RatesConfigurationRegistry();

        $rates->add(new Configuration('EUR', 'median', 'source_1'));
        $rates->add(new Configuration('CHF', 'median', 'source_2'));
        $rates->add(new Configuration('USD', 'median', 'source_1'));

        return $rates;
    }

    /**
     * @param bool $empty
     *
     * @return SourcesRegistry
     */
    private function getSources($empty = false)
    {
        $sources = new SourcesRegistry();

        $source1 = $this
            ->getMockBuilder(SourceInterface::class)
            ->getMock();

        $source1->method('getName')
            ->willReturn('source_1');

        $sources->add($source1);


        $source2 = $this
            ->getMockBuilder(SourceInterface::class)
            ->getMock();

        $source2->method('getName')
            ->willReturn('source_2');

        $sources->add($source2);

        return $sources;
    }

    /**
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher()
    {
        $eventDispatcher = $this
            ->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();

        return $eventDispatcher;
    }

    /**
     * @return ManagerInterface
     */
    private function getManager()
    {
        $manager = $this
            ->getMockBuilder(ManagerInterface::class)
            ->getMock();

        return $manager;
    }
}
