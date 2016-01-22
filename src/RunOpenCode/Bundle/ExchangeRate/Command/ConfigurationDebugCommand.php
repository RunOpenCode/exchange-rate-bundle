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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

    /**
     * @var SymfonyStyle
     */
    protected $sfStyle;

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
        $this->sfStyle = new SymfonyStyle($input, $output);

        $this
            ->sfStyle
            ->title('Displaying current configuration for currency exchange bundle');

        $this
            ->displaySources($output)
            ->displayNewLine($output)
            ->displayProcessors($output)
            ->displayNewLine($output)
            ->displayRepository($output)
            ->displayNewLine($output)
            ->displayRates($output)
            ->displayNewLine($output)
        ;

        $this
            ->sfStyle
            ->success('Configuration is valid.');
    }

    /**
     * Display sources.
     *
     * @param OutputInterface $output
     * @return ConfigurationDebugCommand $this
     */
    protected function displaySources(OutputInterface $output)
    {
        $this->sfStyle->section('Sources:');
        /**
         * @var SourceInterface $source
         */
        foreach ($this->sourcesRegistry as $source) {
            $output->writeln(' -> ' . sprintf('%s as %s', get_class($source), $source->getName()));
        }

        return $this;
    }

    /**
     * Display processors.
     *
     * @param OutputInterface $output
     * @return ConfigurationDebugCommand $this
     */
    protected function displayProcessors(OutputInterface $output)
    {
        $this->sfStyle->section('Processors:');
        /**
         * @var ProcessorInterface $processor
         */
        foreach ($this->processorsRegistry as $processor) {
            $output->writeln(' -> ' . get_class($processor));
        }

        return $this;
    }

    /**
     * Display repository.
     *
     * @param OutputInterface $output
     * @return ConfigurationDebugCommand $this
     */
    protected function displayRepository(OutputInterface $output)
    {
        $this->sfStyle->section('Repository:');

        $output->writeln(' -> '. sprintf('%s with %s record(s).', get_class($this->repository), $this->repository->count()));

        return $this;
    }

    /**
     * Display rates.
     *
     * @param OutputInterface $output
     * @return ConfigurationDebugCommand $this
     */
    protected function displayRates(OutputInterface $output)
    {
        $this->sfStyle->section('Exchange rates:');

        $table = new Table($output);

        $table->setHeaders(array(
            array(new TableCell('Registered currency rates', array('colspan' => 3))),
            array('Currency code', 'Rate type', 'Source')
        ));
        /**
         * @var Configuration $rateConfiguration
         */
        foreach ($this->ratesConfigurationRegistry as $rateConfiguration) {
            $table->addRow(array(
                $rateConfiguration->getCurrencyCode(),
                $rateConfiguration->getRateType(),
                $rateConfiguration->getSourceName()
            ));
        }

        $table->render();

        return $this;
    }


    /**
     * Display new line.
     *
     * @param OutputInterface $output
     * @return ConfigurationDebugCommand $this
     */
    protected function displayNewLine(OutputInterface $output)
    {
        $this->sfStyle->newLine();
        return $this;
    }
}
