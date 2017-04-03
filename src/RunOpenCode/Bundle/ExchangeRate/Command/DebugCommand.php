<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
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
 * Class DebugCommand
 *
 * Display current exchange rate configuration.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Command
 */
class DebugCommand extends Command
{
    /**
     * @var string
     */
    protected $baseCurrency;

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
    protected $output;

    /**
     * @var bool
     */
    protected $valid;

    public function __construct(
        $baseCurrency,
        SourcesRegistryInterface $sourcesRegistry,
        ProcessorsRegistryInterface $processorsRegistry,
        RatesConfigurationRegistryInterface $ratesConfigurationRegistry,
        RepositoryInterface $repository
    ) {
        parent::__construct();

        $this->baseCurrency = $baseCurrency;
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
            ->setName('debug:runopencode:exchange-rate')
            ->setAliases([
                'debug:exchange-rate',
                'debug:roc:exchange-rate'
            ])
            ->setDescription('Debug exchange rate bundle configuration.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = new SymfonyStyle($input, $output);
        $this->valid = true;

        $this->output->title(sprintf('Debugging current configuration for currency exchange bundle with base currency "%s"', $this->baseCurrency));

        $this->displaySources();
        $this->output->newLine();
        $this->displayProcessors();
        $this->output->newLine();
        $this->displayProcessors();
        $this->output->newLine();
        $this->displayRepository();
        $this->output->newLine();
        $this->displayRates();

        $this->output->section('Summary:');

        if ($this->valid) {
            $this->output->success('Configuration is valid.');
            return 0;
        }

        $this->output->error('Configuration is not valid!');
        return -1;
    }

    /**
     * Display sources.
     *
     * @return DebugCommand $this
     */
    protected function displaySources()
    {
        $this->output->section('Sources:');

        $headers = ['Name', 'Class'];

        $rows = [];

        /**
         * @var SourceInterface $source
         */
        foreach ($this->sourcesRegistry as $source) {

            $rows[] = [
                $source->getName(),
                get_class($source)
            ];
        }

        if (count($rows) > 0) {
            $this->output->table($headers, $rows);
            return $this;
        }

        $this->valid = false;
        $this->output->error('There are no registered sources.');
        return $this;
    }

    /**
     * Display processors.
     *
     * @return DebugCommand $this
     */
    protected function displayProcessors()
    {
        $this->output->section('Processors:');

        $processors = [];

        /**
         * @var ProcessorInterface $processor
         */
        foreach ($this->processorsRegistry as $processor) {
            $processors[] = get_class($processor);
        }

        if (count($processors) > 0) {
            $this->output->writeln(sprintf('Registered processors implementations: "%s"', implode('", "', $processors)));
            return $this;
        }

        $this->output->warning('There are no registered processors.');
        return $this;
    }

    /**
     * Display repository.
     *
     * @return DebugCommand $this
     */
    protected function displayRepository()
    {
        $this->output->section('Repository:');

        $this->output->writeln(sprintf('Registered "%s" repository implementation with "%s" records so far.', get_class($this->repository), $this->repository->count()));

        return $this;
    }

    /**
     * Display rates.
     *
     * @return DebugCommand $this
     */
    protected function displayRates()
    {
        $this->output->section('Registered exchange rates:');

        $headers = ['Currency code', 'Rate type', 'Source', 'Source exists'];

        $rows = [];
        $missingSources = [];
        /**
         * @var Configuration $rateConfiguration
         */
        foreach ($this->ratesConfigurationRegistry as $rateConfiguration) {

            $sourceExists = $this->sourcesRegistry->has($rateConfiguration->getSourceName());

            $rows[] = [
                $rateConfiguration->getCurrencyCode(),
                $rateConfiguration->getRateType(),
                $rateConfiguration->getSourceName(),
                $sourceExists  ? 'Yes' : 'No',
            ];

            if (!$sourceExists) {
                $missingSources[] = $rateConfiguration->getSourceName();
            }
        }

        if (count($rows) > 0) {
            $this->output->table($headers, $rows);

            if (count($missingSources) > 0) {
                $this->valid = false;
                $this->output->error(sprintf('Missing sources detected: "%s".', implode('", "', $missingSources)));
            }

            return $this;
        }

        $this->valid = false;
        $this->output->error('There are no registered currency exchange rates.');
        return $this;
    }
}
