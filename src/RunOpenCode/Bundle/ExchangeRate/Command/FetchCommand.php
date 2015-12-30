<?php

namespace RunOpenCode\Bundle\ExchangeRate\Command;

use RunOpenCode\ExchangeRate\Contract\ManagerInterface;
use RunOpenCode\ExchangeRate\Contract\SourceInterface;
use RunOpenCode\ExchangeRate\Contract\SourcesRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCommand extends Command
{
    protected $manager;

    protected $sourcesRegistry;

    public function __construct(ManagerInterface $manager, SourcesRegistryInterface $sourcesRegistry)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->sourcesRegistry = $sourcesRegistry;
    }

    protected function configure()
    {
        $this
            ->setName('roc:exchange-rate:fetch')
            ->setDescription('Fetch exchange rates from sources.')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'State on which date exchange rates should be fetched.')
            ->addOption('source', 's', InputOption::VALUE_OPTIONAL, 'State which sources should be contacted only, separated with comma.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $sources = $this->parseSources($input, $output);
            $date = $this->parseDate($input, $output);
        } catch (\Exception $e) {
            return;
        }

        $this->manager->fetch($sources, $date);
    }

    protected function parseDate(InputInterface $input, OutputInterface $output)
    {
        $date = $input->getOption('date');

        if (!empty($date)) {
            $date = \DateTime::createFromFormat('Y-m-d', $date);

            if ($date === false) {
                $output->writeln('<error>Invalid date format provided, expected format is "Y-m-d".</error>');
                throw new \Exception;
            }
        }

        return $date;
    }

    protected function parseSources(InputInterface $input, OutputInterface $output)
    {
        $sources = $input->getOption('source');

        if (!empty($sources)) {
            $sources = array_map('trim', explode(',', $sources));

            foreach ($sources as $source) {

                if (!$this->sourcesRegistry->has($source)) {
                    $output->writeln(sprintf('<error>Invalid source name "%s" provided, available sources are "%s".</error>', $source, implode(', ', array_map(function(SourceInterface $source) {
                        return $source->getName();
                    }, $this->sourcesRegistry->all()))));
                    throw new \Exception;
                }
            }
        }

        return $sources;
    }
}