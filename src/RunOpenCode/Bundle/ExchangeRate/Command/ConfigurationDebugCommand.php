<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Command;

use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\ProcessorInterface;
use RunOpenCode\ExchangeRate\Contract\ProcessorsRegistryInterface;
use RunOpenCode\ExchangeRate\Contract\RatesConfigurationRegistryInterface;
use RunOpenCode\ExchangeRate\Contract\RepositoryInterface;
use RunOpenCode\ExchangeRate\Contract\SourceInterface;
use RunOpenCode\ExchangeRate\Contract\SourcesRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ConfigurationDebugCommand
 *
 * Display current exchange rate configuration.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Command
 */
class ConfigurationDebugCommand extends Command
{
    /**
     * @var SourcesRegistryInterface
     */
    protected $sourcesRegistry;

    /**
     * @var ProcessorsRegistryInterface
     */
    protected $processorsRegistry;

    /**
     * @var RatesConfigurationRegistryInterface
     */
    protected $ratesConfigurationRegistry;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    public function __construct(
        SourcesRegistryInterface $sourcesRegistry,
        ProcessorsRegistryInterface $processorsRegistry,
        RatesConfigurationRegistryInterface $ratesConfigurationRegistry,
        RepositoryInterface $repository
    ) {
        parent::__construct();
        $this->sourcesRegistry = $sourcesRegistry;
        $this->processorsRegistry = $processorsRegistry;
        $this->ratesConfigurationRegistry = $ratesConfigurationRegistry;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('roc:exchange-rate:configuration-debug')
            ->setDescription('Dump current configuration.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputStyle = new SymfonyStyle($input, $output);

        $outputStyle->title('Displaying current configuration for currency exchange bundle');

        $this
            ->displaySources($outputStyle)
            ->displayNewLine($outputStyle)
            ->displayProcessors($outputStyle)
            ->displayNewLine($outputStyle)
            ->displayRepository($outputStyle)
            ->displayNewLine($outputStyle)
            ->displayRates($outputStyle)
            ->displayNewLine($outputStyle)
        ;

        $outputStyle
            ->success('Configuration is valid.');
    }

    /**
     * Display sources.
     *
     * @param OutputStyle $outputStyle Console output style.
     * @return ConfigurationDebugCommand $this
     */
    protected function displaySources(OutputStyle $outputStyle)
    {
        $outputStyle->section('Sources:');
        /**
         * @var SourceInterface $source
         */
        foreach ($this->sourcesRegistry as $source) {
            $outputStyle->writeln(' -> ' . sprintf('%s as %s', get_class($source), $source->getName()));
        }

        return $this;
    }

    /**
     * Display processors.
     *
     * @param OutputStyle $outputStyle Console output style.
     * @return ConfigurationDebugCommand $this
     */
    protected function displayProcessors(OutputStyle $outputStyle)
    {
        $outputStyle->section('Processors:');
        /**
         * @var ProcessorInterface $processor
         */
        foreach ($this->processorsRegistry as $processor) {
            $outputStyle->writeln(' -> ' . get_class($processor));
        }

        return $this;
    }

    /**
     * Display repository.
     *
     * @param OutputStyle $outputStyle Console output style.
     * @return ConfigurationDebugCommand $this
     */
    protected function displayRepository(OutputStyle $outputStyle)
    {
        $outputStyle->section('Repository:');

        $outputStyle->writeln(' -> '. sprintf('%s with %s record(s).', get_class($this->repository), $this->repository->count()));

        return $this;
    }

    /**
     * Display rates.
     *
     * @param OutputStyle $outputStyle Console output style.
     * @return ConfigurationDebugCommand $this
     */
    protected function displayRates(OutputStyle $outputStyle)
    {
        $outputStyle->section('Registered exchange rates:');

        $headers = array('Currency code', 'Rate type', 'Source');

        $rows = array();
        /**
         * @var Configuration $rateConfiguration
         */
        foreach ($this->ratesConfigurationRegistry as $rateConfiguration) {
            $rows[] = array(
                $rateConfiguration->getCurrencyCode(),
                $rateConfiguration->getRateType(),
                $rateConfiguration->getSourceName()
            );
        }

        $outputStyle->table($headers, $rows);

        return $this;
    }


    /**
     * Display new line.
     *
     * @param OutputStyle $outputStyle Console output style.
     * @return ConfigurationDebugCommand $this
     */
    protected function displayNewLine(OutputStyle $outputStyle)
    {
        $outputStyle->newLine();
        return $this;
    }
}
