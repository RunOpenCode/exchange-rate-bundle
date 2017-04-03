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
use RunOpenCode\Bundle\ExchangeRate\Command\DebugCommand;
use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\ProcessorInterface;
use RunOpenCode\ExchangeRate\Contract\ProcessorsRegistryInterface;
use RunOpenCode\ExchangeRate\Contract\RatesConfigurationRegistryInterface;
use RunOpenCode\ExchangeRate\Contract\RepositoryInterface;
use RunOpenCode\ExchangeRate\Contract\SourceInterface;
use RunOpenCode\ExchangeRate\Contract\SourcesRegistryInterface;
use RunOpenCode\ExchangeRate\Registry\ProcessorsRegistry;
use RunOpenCode\ExchangeRate\Registry\RatesConfigurationRegistry;
use RunOpenCode\ExchangeRate\Registry\SourcesRegistry;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;

class DebugCommandTest extends TestCase
{
    /**
     * @test
     */
    public function itSuccessfullyExecutes()
    {
        $application = $this->buildApplication();

        $returnValue = $application->doRun(new ArrayInput([]), new NullOutput());
        $this->assertEquals(0, $returnValue);
    }

    /**
     * @test
     */
    public function itErrorsWhenThereAreNoSources()
    {
        $application = $this->buildApplication($this->getSources(true));

        $returnValue = $application->doRun(new ArrayInput([]), $buffer = new BufferedOutput());
        $this->assertEquals(-1, $returnValue);

        $this->assertContains('There are no registered sources.', $buffer->fetch());
    }

    /**
     * @test
     */
    public function itWarnsWhenThereAreNoProcessors()
    {
        $application = $this->buildApplication(null, $this->getProcessors(true));

        $returnValue = $application->doRun(new ArrayInput([]), $buffer = new BufferedOutput());
        $this->assertEquals(0, $returnValue);

        $this->assertContains('There are no registered processors.', $buffer->fetch());
    }

    /**
     * @test
     */
    public function itErrorsWhenThereAreNoConfiguredRates()
    {
        $application = $this->buildApplication(null, null, $this->getRates(true));

        $returnValue = $application->doRun(new ArrayInput([]), $buffer = new BufferedOutput());
        $this->assertEquals(-1, $returnValue);

        $this->assertContains('There are no registered currency exchange rates.', $buffer->fetch());
    }

    /**
     * @test
     */
    public function itErrorsWhenThereIsMissingSourcesForConfiguredRates()
    {
        $rates = $this->getRates();
        $rates->add(new Configuration('HUF', 'median', 'mising_source'));

        $application = $this->buildApplication(null, null, $rates);

        $returnValue = $application->doRun(new ArrayInput([]), $buffer = new BufferedOutput());
        $this->assertEquals(-1, $returnValue);

        $this->assertContains('Missing sources detected: "mising_source".', $buffer->fetch());
    }

    /**
     * @param null|SourcesRegistryInterface $sources
     * @param null|ProcessorsRegistryInterface $processors
     * @param null|RatesConfigurationRegistryInterface $rates
     * @param null|RepositoryInterface $repository
     *
     * @return Application
     */
    private function buildApplication($sources = null, $processors = null, $rates = null, $repository = null)
    {
        $application = new Application();

        $command = new DebugCommand(
            'RSD',
            $sources ?? $this->getSources(false),
            $processors ?? $this->getProcessors(false),
            $rates ?? $this->getRates(false),
            $repository ?? $this->getRepository(false)
        );

        $application->add($command);
        $application->setDefaultCommand($command->getName());

        return $application;
    }

    /**
     * @param bool $empty
     *
     * @return SourcesRegistry
     */
    private function getSources($empty = false)
    {
        $sources = new SourcesRegistry();

        if (!$empty) {

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
        }

        return $sources;
    }

    /**
     * @param bool $empty
     *
     * @return ProcessorsRegistry
     */
    private function getProcessors($empty = false)
    {
        $processors = new ProcessorsRegistry();

        if (!$empty) {

            $processor1 = $this
                ->getMockBuilder(ProcessorInterface::class)
                ->getMock();

            $processors->add($processor1);

            $processor2 = $this
                ->getMockBuilder(ProcessorInterface::class)
                ->getMock();

            $processors->add($processor2);
        }

        return $processors;
    }

    /**
     * @param bool $empty
     *
     * @return RatesConfigurationRegistry
     */
    private function getRates($empty = false)
    {
        $rates = new RatesConfigurationRegistry();

        if (!$empty) {
            $rates->add(new Configuration('EUR', 'median', 'source_1'));
            $rates->add(new Configuration('CHF', 'median', 'source_2'));
            $rates->add(new Configuration('USD', 'median', 'source_1'));
        }

        return $rates;
    }

    /**
     * @param bool $empty
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getRepository($empty = false)
    {
        $repository = $this
            ->getMockBuilder(RepositoryInterface::class)
            ->getMock();

        if (!$empty) {
            $repository
                ->method('count')
                ->willReturn(1000);
        }

        return $repository;
    }
}
