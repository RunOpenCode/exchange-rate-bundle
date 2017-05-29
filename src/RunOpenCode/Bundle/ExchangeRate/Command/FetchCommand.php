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

use RunOpenCode\Bundle\ExchangeRate\Event\FetchErrorEvent;
use RunOpenCode\Bundle\ExchangeRate\Event\FetchEvents;
use RunOpenCode\Bundle\ExchangeRate\Event\FetchSuccessEvent;
use RunOpenCode\Bundle\ExchangeRate\Exception\InvalidArgumentException;
use RunOpenCode\Bundle\ExchangeRate\Exception\RuntimeException;
use RunOpenCode\ExchangeRate\Contract\ManagerInterface;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Contract\SourceInterface;
use RunOpenCode\ExchangeRate\Contract\SourcesRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class FetchCommand
 *
 * Fetch rates from sources.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Command
 */
class FetchCommand extends Command
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @var SourcesRegistryInterface
     */
    protected $sourcesRegistry;

    /**
     * @var SymfonyStyle
     */
    protected $output;

    public function __construct(EventDispatcherInterface $eventDispatcher, ManagerInterface $manager, SourcesRegistryInterface $sourcesRegistry)
    {
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
        $this->manager = $manager;
        $this->sourcesRegistry = $sourcesRegistry;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('runopencode:exchange-rate:fetch')
            ->setAliases([
                'roc:exchange-rate:fetch'
            ])
            ->setDescription('Fetch exchange rates from sources.')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'State on which date exchange rates should be fetched.')
            ->addOption('source', 'src', InputOption::VALUE_OPTIONAL, 'State which sources should be contacted only, separated with comma.')
            ->addOption('silent', null, InputOption::VALUE_OPTIONAL, 'In silent mode, rates are fetched, but no event will be fired.', false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = new SymfonyStyle($input, $output);
        $date = (null !== $input->getOption('date')) ? $this->sanitizeDate($input->getOption('date')) : new \DateTime('now');
        $sources = $this->sanitizeSources($input->getOption('source'));
        $this->output->title(sprintf('Fetching rates for sources "%s" on "%s".', implode('", "', $sources), $date->format('Y-m-d')));

        $errors = [];
        $fetched = [];

        foreach ($sources as $source) {

            try {
                $rates = $this->manager->fetch($source, $date);

                if (0 === count($rates)) {
                    throw new RuntimeException(sprintf('No rate fetched from source "%s".', $source));
                }

                $rows = array_map(function(RateInterface $rate) {
                    return [
                        $rate->getCurrencyCode(),
                        $rate->getRateType(),
                        $rate->getValue(),
                    ];
                }, $rates);

                $this->output->section(sprintf('Fetched rates for source "%s":', $source));
                $this->output->table(['Currency code', 'Rate type', 'Value'], $rows);

                $fetched[$source] = $rates;

            } catch (\Exception $e) {
                $this->output->error(sprintf('Could not fetch rates from source "%s" (%s).', $source, $e->getMessage()));
                $errors[$source] = $e;
            }
        }

        if (!$input->getOption('silent')) {

            if (count($fetched) > 0) {
                $this->eventDispatcher->dispatch(FetchEvents::SUCCESS, new FetchSuccessEvent($fetched, $date));
            }

            if (count($errors) > 0) {
                $this->eventDispatcher->dispatch(FetchEvents::ERROR, new FetchErrorEvent($errors, $date));
            }
        }

        if ($errors) {
            $this->output->error('Could not fetch all rates.');
            return -1;
        }

        $this->output->success('Rates successfully fetched.');
        return 0;
    }

    /**
     * Sanitizes a date from console input.
     *
     * @param string|\DateTime $dateString A date
     *
     * @return \DateTime
     *
     * @throws InvalidArgumentException
     */
    protected function sanitizeDate($dateString)
    {

        $date = \DateTime::createFromFormat('Y-m-d', $dateString);

        if ($date instanceof \DateTime) {
            return $date;
        }

        try {
            $date = new \DateTime($dateString);
        } catch (\Exception $e) {
            // noop
        }


        if ($date instanceof \DateTime) {
            return $date;
        }

        throw new InvalidArgumentException(sprintf('Provided date "%s" is provided in unknown format. You should use "Y-m-d" instead.', $dateString));
    }

    /**
     * Clean sources from console input.
     *
     * @param mixed $sourcesString A sources.
     *
     * @return array|null
     *
     * @throws InvalidArgumentException
     */
    protected function sanitizeSources($sourcesString)
    {
        $sources = $sourcesString;

        if (is_string($sources)) {
            $sources = array_map('trim', explode(',', $sources));
        }

        if (null === $sources || (is_array($sources) && count($sources) === 0)) {

            return array_map(function(SourceInterface $source) {
                return $source->getName();
            }, $this->sourcesRegistry->all());
        }

        foreach ($sources as $source) {
            if (!$this->sourcesRegistry->has($source)) {
                throw new InvalidArgumentException(sprintf('Unknown source "%s" provided.', $source));
            }
        }

        return $sources;
    }
}
