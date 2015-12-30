<?php

namespace RunOpenCode\Bundle\ExchangeRate\Command;

use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\ProcessorsRegistryInterface;
use RunOpenCode\ExchangeRate\Contract\RatesConfigurationRegistryInterface;
use RunOpenCode\ExchangeRate\Contract\SourcesRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigurationDebugCommand extends Command
{
    protected $sourcesRegistry;

    protected $processorsRegistry;

    protected $ratesConfigurationRegistry;

    public function __construct(SourcesRegistryInterface $sourcesRegistry, ProcessorsRegistryInterface $processorsRegistry, RatesConfigurationRegistryInterface $ratesConfigurationRegistry)
    {
        parent::__construct();

        $this->sourcesRegistry = $sourcesRegistry;
        $this->processorsRegistry = $processorsRegistry;
        $this->ratesConfigurationRegistry = $ratesConfigurationRegistry;
    }

    protected function configure()
    {
        $this
            ->setName('roc:exchange-rate:configuration-debug')
            ->setDescription('Dump current configuration.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');

        $output->writeln($formatter->formatBlock(array(
            '-------------------------------------------------------------',
            'Displaying current configuration for currency exchange bundle',
            '-------------------------------------------------------------'
        ), 'info'));

        $output->writeln('');

        $this->displaySources($output);

        $output->writeln('');

        $this->displayProcessors($output);

        $output->writeln('');

        $this->displayRates($output);

        $output->writeln('');

        $output->writeln($formatter->formatBlock('-------------------------------------------------------------', 'info'));

    }

    protected function displaySources(OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');

        foreach ($this->sourcesRegistry as $source) {
            $output->writeln($formatter->formatSection('Source', sprintf('%s as %s', get_class($source), $source->getName())));
        }
    }

    protected function displayProcessors(OutputInterface $output)
    {
        $formatter = $this->getHelper('formatter');

        foreach ($this->processorsRegistry as $processor) {
            $output->writeln($formatter->formatSection('Processor', get_class($processor)));
        }
    }

    protected function displayRates(OutputInterface $output)
    {
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
                $rateConfiguration->getSource()
            ));
        }

        $table->render();
    }
}
