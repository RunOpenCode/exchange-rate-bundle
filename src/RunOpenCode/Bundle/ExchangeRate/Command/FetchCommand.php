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

use RunOpenCode\Bundle\ExchangeRate\Contract\NotificationInterface;
use RunOpenCode\ExchangeRate\Contract\ManagerInterface;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Contract\SourceInterface;
use RunOpenCode\ExchangeRate\Contract\SourcesRegistryInterface;
use RunOpenCode\ExchangeRate\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Style\OutputStyle;

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

    /**
     * @var SymfonyStyle
     */
    protected $outputStyle;

    /**
     * @var NotificationInterface[]
     */
    protected $successNotifications;

    /**
     * @var NotificationInterface[]
     */
    protected $errorNotifications;

    public function __construct(ManagerInterface $manager, SourcesRegistryInterface $sourcesRegistry)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->sourcesRegistry = $sourcesRegistry;
        $this->successNotifications = [];
        $this->errorNotifications = [];
    }

    /**
     * Add success notification to command notifications stack.
     *
     * @param NotificationInterface $notification Success notification to add.
     * @return FetchCommand $this Fluent interface.
     */
    public function addSuccessNotification(NotificationInterface $notification)
    {
        $this->successNotifications[] = $notification;
        return $this;
    }

    /**
     * Add error notification to command notifications stack.
     *
     * @param NotificationInterface $notification Error notification to add.
     * @return FetchCommand $this Fluent interface.
     */
    public function addErrorNotification(NotificationInterface $notification)
    {
        $this->errorNotifications[] = $notification;
        return $this;
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
            ->addOption('source', 'src', InputOption::VALUE_OPTIONAL, 'State which sources should be contacted only, separated with comma.')
            ->addOption('silent', null, InputOption::VALUE_OPTIONAL, 'In silent mode, rates are fetched, but no notification is being fired on any event.', false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputStyle = new SymfonyStyle($input, $output);

        try {
            $this
                ->cleanInputDate($input, $outputStyle)
                ->cleanInputSources($input, $outputStyle);
        } catch (\Exception $e) {

            $this->getLogger()->critical('Unable to execute command. Reason: "{message}".', array(
                'message' => $e->getMessage(),
                'exception' => $e
            ));

            return;
        }

        $this->displayCommandBegin($input, $outputStyle);

        try {

            $rates = $this->doFetch($input);

        } catch (\Exception $e) {

            $this->displayCommandError($outputStyle);

            if (!$input->getOption('silent')) {
                $this->dispatchErrorNotifications($input->getOption('source'), $input->getOption('date'));
            }

            $this->getLogger()->critical('Unable to fetch rates. Reason: "{message}".', array(
                'message' => $e->getMessage(),
                'exception' => $e
            ));

            return;
        }

        $this->displayCommandSuccess($outputStyle);

        if (!$input->getOption('silent')) {
            $this->dispatchSuccessNotifications($input->getOption('source'), $input->getOption('date'), $rates);
        }

        $this->getLogger()->info('Successfully fetched rates "{rates}".', array(
            'rates' => implode(', ', array_map(function(RateInterface $rate) {
                return sprintf('%s => %s', $rate->getBaseCurrencyCode(), $rate->getCurrencyCode());
            }, $rates))
        ));
    }

    /**
     * Clean date from console input.
     *
     * @param InputInterface $input Console input.
     * @param OutputStyle $outputStyle Output style to use.
     * @return FetchCommand $this Fluent interface.
     *
     * @throws \Exception
     */
    protected function cleanInputDate(InputInterface $input, OutputStyle $outputStyle)
    {
        $date = $input->getOption('date');

        if (!empty($date)) {
            $date = \DateTime::createFromFormat('Y-m-d', $date);

            if ($date === false) {
                $outputStyle->error('Invalid date format provided, expected format is "Y-m-d".');
                throw new \Exception;
            }
        } else {
            $date = new \DateTime('now');
        }

        $input->setOption('date', $date);

        return $this;
    }

    /**
     * Clean sources from console input.
     *
     * @param InputInterface $input Console input.
     * @param OutputStyle $outputStyle Output style to use.
     * @return FetchCommand $this Fluent interface.
     *
     * @throws \Exception
     */
    protected function cleanInputSources(InputInterface $input, OutputStyle $outputStyle)
    {
        $sources = $input->getOption('source');

        if (!empty($sources)) {
            $sources = array_map('trim', explode(',', $sources));

            foreach ($sources as $source) {

                if (!$this->sourcesRegistry->has($source)) {

                    $outputStyle->error(sprintf('Invalid source name "%s" provided, available sources are "%s".', $source, implode(', ', array_map(function(SourceInterface $source) {
                        return $source->getName();
                    }, $this->sourcesRegistry->all()))));

                    throw new \Exception;
                }
            }
        }

        $input->setOption('source', $sources);

        return $this;
    }

    /**
     * Display command begin note.
     *
     * @param InputInterface $input Console input.
     * @param OutputStyle $outputStyle Console style.
     * @return FetchCommand $this Fluent interface.
     */
    protected function displayCommandBegin(InputInterface $input, OutputStyle $outputStyle)
    {
        $outputStyle->title('Exchange rates:');
        $outputStyle->text(
            sprintf(
                'Fetching from %s for date %s....',
                ($input->getOption('source') ? sprintf('"%s"', implode('", "', $input->getOption('source'))) : 'all sources'),
                $input->getOption('date')->format('Y-m-d')
            )
        );

        return $this;
    }

    /**
     * Do fetch rates.
     *
     * @param InputInterface $input Console input.
     * @return RateInterface[] Fetched rates.
     * @throws \Exception
     */
    protected function doFetch(InputInterface $input)
    {
        try {

            $rates = $this->manager->fetch($input->getOption('source'), $input->getOption('date'));

            $this->getLogger()->info(sprintf('Rates fetched from %s for date %s.', $input->getOption('source') ? sprintf('"%s"', implode('", "', $input->getOption('source'))) : 'all sources', $input->getOption('date')->format('Y-m-d')));

        } catch (\Exception $e) {

            $this->getLogger()->critical('Unable to fetch rates.', array(
                'date' => $input->getOption('date')->format('Y-m-d'),
                'sources' => $input->getOption('source') ? sprintf('"%s"', implode('", "', $input->getOption('source'))) : 'All sources',
                'exception' => array(
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                )
            ));

            throw $e;
        }

        return $rates;
    }

    /**
     * Display command success note.
     *
     * @param OutputStyle $outputStyle
     * @return FetchCommand $this Fluent interface.
     */
    protected function displayCommandSuccess(OutputStyle $outputStyle)
    {
        $outputStyle->success('Exchange rates successfully fetched.');
        return $this;
    }

    /**
     * Display command error note.
     *
     * @param OutputStyle $outputStyle
     * @return FetchCommand $this Fluent interface.
     */
    protected function displayCommandError(OutputStyle $outputStyle)
    {
        $outputStyle->error('Unable to fetch data from source(s). See log for details.');
        return $this;
    }

    /**
     * Dispatch success notifications.
     *
     * @param null|array $source Sources for which command is executed.
     * @param \DateTime $date Date for which rates are fetched.
     * @param RateInterface[] $rates Fetched rates
     * @return FetchCommand $this Fluent interface.
     */
    protected function dispatchSuccessNotifications($source, \DateTime $date, array $rates)
    {
        foreach ($this->successNotifications as $notification) {

            $notification->notify(array(
                'source' => $source,
                'date' => $date,
                'rates' => $rates
            ));
        }

        return $this;
    }

    /**
     * Dispatch error notifications.
     *
     * @param null|array $source Sources for which command is executed.
     * @param \DateTime $date Date for which rates are fetched.
     * @return FetchCommand $this Fluent interface.
     */
    protected function dispatchErrorNotifications($source, \DateTime $date)
    {
        foreach ($this->errorNotifications as $notification) {

            $notification->notify(array(
                'source' => $source,
                'date' => $date
            ));
        }

        return $this;
    }
}
