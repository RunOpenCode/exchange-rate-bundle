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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Templating\EngineInterface;

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
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var EngineInterface
     */
    protected $templateEngine;

    /**
     * @var SymfonyStyle
     */
    protected $outputStyle;

    public function __construct(ManagerInterface $manager, SourcesRegistryInterface $sourcesRegistry)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->sourcesRegistry = $sourcesRegistry;
    }

    public function setMailer(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }

    public function setTemplateEngine(EngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
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
            ->addOption('source', 's', InputOption::VALUE_OPTIONAL, 'State which sources should be contacted only, separated with comma.')
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
            return;
        }

        try {
            $this
                ->displayCommandBegin($input, $outputStyle)
                ->doFetch($input)
                ->displayCommandSuccess($outputStyle)
                ->notifySuccess();
            ;
        } catch (\Exception $e) {
            $this
                ->displayCommandFail($outputStyle)
                ->notifyFail();
        }
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
     * @return FetchCommand $this Fluent interface.
     * @throws \Exception
     */
    protected function doFetch(InputInterface $input)
    {
        try {

            $this->manager->fetch($input->getOption('source'), $input->getOption('date'));

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

        return $this;
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

    protected function notifySuccess()
    {

        return $this;
    }

    /**
     * Display command fail note.
     *
     * @param OutputStyle $outputStyle
     * @return FetchCommand $this Fluent interface.
     */
    protected function displayCommandFail(OutputStyle $outputStyle)
    {
        $outputStyle->error('Unable to fetch data from source(s). See log for details.');

        return $this;
    }

    protected function notifyFail()
    {
        return $this;
    }
}
