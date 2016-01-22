<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection;

use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Configuration as TreeConfiguration;
use RunOpenCode\ExchangeRate\Utils\CurrencyCodeUtil;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class Extension
 *
 * Bundle extension.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection
 */
class Extension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return "run_open_code_exchange_rate";
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {

        $configuration = new TreeConfiguration();
        $config = $this->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('repository.xml');
        $loader->load('command.xml');
        $loader->load('controller.xml');
        $loader->load('form_type.xml');
        $loader->load('manager.xml');
        $loader->load('processor.xml');
        $loader->load('source.xml');
        $loader->load('validator.xml');

        $this
            ->configureBaseCurrency($config, $container)
            ->configureRepository($config, $container)
            ->configureFileRepository($config, $container)
            ->configureController($config, $container)
            ->configureRates($config, $container)
            ->configureSimpleSources($config, $container)
        ;
    }

    /**
     * Configure base currency.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     * @return Extension $this Fluent interface.
     */
    protected function configureBaseCurrency(array $config, ContainerBuilder $container)
    {
        $baseCurrency = CurrencyCodeUtil::clean($config['base_currency']);
        $container->setParameter('run_open_code.exchange_rate.base_currency', $baseCurrency);

        return $this;
    }

    /**
     * Configure rates.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     * @return Extension $this Fluent interface.
     */
    protected function configureRates(array $config, ContainerBuilder $container)
    {
        if (count($config['rates']) === 0) {
            throw new \RuntimeException('You have to configure which rates you would like to use in your project.');
        }

        $container->setParameter('run_open_code.exchange_rate.registered_rates', $config['rates']);
        return $this;
    }

    /**
     * Configure required processors.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     * @return Extension $this Fluent interface.
     */
    protected function configureRepository(array $config, ContainerBuilder $container)
    {
        $container->setParameter('run_open_code.exchange_rate.repository', $config['repository']);
        return $this;
    }

    /**
     * Configure file repository, if used.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     * @return Extension $this Fluent interface.
     */
    protected function configureFileRepository(array $config, ContainerBuilder $container)
    {
        $container->setParameter('run_open_code.exchange_rate.repository.file_repository.path', $config['file_repository']['path']);
        return $this;
    }

    /**
     * Configure controller.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     * @return Extension $this Fluent interface.
     */
    protected function configureController(array $config, ContainerBuilder $container)
    {
        $container->setParameter(
            'run_open_code.exchange_rate.controller.view_configuration',
            $config['view']
        );

        return $this;
    }

    /**
     * Configure simple sources which does not have to be explicitly added to service container.
     *
     * @param array $config Configuration parameters.
     * @param ContainerBuilder $container Service container.
     * @return Extension $this Fluent interface.
     */
    protected function configureSimpleSources(array $config, ContainerBuilder $container)
    {
        if (!empty($config['sources']) && count($config['sources']) > 0) {
            $container->setParameter('run_open_code.exchange_rate.source.registered_simple_sources', $config['sources']);
        }
    }
}
