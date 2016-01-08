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

use RunOpenCode\ExchangeRate\Contract\ManagerInterface;
use RunOpenCode\ExchangeRate\Contract\SourceInterface;
use RunOpenCode\ExchangeRate\Contract\SourcesRegistryInterface;
use RunOpenCode\ExchangeRate\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FetchCommand
 *
 * Fetch rates from sources.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Command
 */
class FetchCommand extends Command
{
    use LoggerAwareTrait;

    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @var SourcesRegistryInterface
     */
    protected $sourcesRegistry;

    public function __construct(ManagerInterface $manager, SourcesRegistryInterface $sourcesRegistry)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->sourcesRegistry = $sourcesRegistry;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('roc:exchange-rate:fetch')
            ->setDescription('Fetch exchange rates from sources.')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'State on which date exchange rates should be fetched.')
            ->addOption('source', 's', InputOption::VALUE_OPTIONAL, 'State which sources should be contacted only, separated with comma.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $sources = $this->parseSources($input, $output);
            $date = $this->parseDate($input, $output);
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to continue.</error>');
            return;
        }

        $output->writeln($this->getFormatter()->formatSection('Exchange rates', sprintf('Fetching from %s sources for date %s.', ($sources ? implode(', ', $sources) : 'all'),  $date->format('Y-m-d'))));

        try {

            $this->manager->fetch($sources, $date);

            $output->writeln($this->getFormatter()->formatSection('Exchange rates', 'Rates fetched.'));

            $this->getLogger()->info(sprintf('Rates fetched from %s sources for date %s.', ($sources) ? implode(', ', $sources) : 'all', $date->format('Y-m-d')));

        } catch (\Exception $e) {
            $this->getLogger()->critical('Unable to fetch rates.', array(
                'date' => date('Y-m-d H:i:s'),
                'sources' => $sources ? 'All' : implode(', ', $sources),
                'exception' => array(
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                )
            ));
            $output->writeln('<error>Unable to fetch.</error>');
            return;
        }
    }

    /**
     * Parse date from console input.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return \DateTime
     *
     * @throws \Exception
     */
    protected function parseDate(InputInterface $input, OutputInterface $output)
    {
        $date = $input->getOption('date');

        if (!empty($date)) {
            $date = \DateTime::createFromFormat('Y-m-d', $date);

            if ($date === false) {
                $output->writeln('<error>Invalid date format provided, expected format is "Y-m-d".</error>');
                throw new \Exception;
            }
        } else {
            $date = new \DateTime('now');
        }

        return $date;
    }

    /**
     * Parse sources from console input.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return array|null
     *
     * @throws \Exception
     */
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

    /**
     * Get formatter.
     *
     * @return FormatterHelper
     */
    protected function getFormatter()
    {
        return $this->getHelper('formatter');
    }
}
