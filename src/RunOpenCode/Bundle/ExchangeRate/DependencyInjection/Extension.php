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

use RunOpenCode\ExchangeRate\Configuration;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

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
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->configureExchangeRateService($config, $container);
        $this->configureSourcesRegistry($config, $container);
        $this->configureProcessorsRegistry($config, $container);
        $this->configureRatesRegistry($config, $container);
        $this->configureFileRepository($config, $container);
        $this->configureController($config, $container);
        $this->configureDebugCommand($config, $container);
    }

    /**
     * Configure exchange rate service.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configureExchangeRateService(array $config, ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate')) {

            $definition = $container->getDefinition('run_open_code.exchange_rate');

            $definition->setArguments(array(
                $config['base_currency'],
                new Reference($config['repository']),
                new Reference('run_open_code.exchange_rate.registry.sources'),
                new Reference('run_open_code.exchange_rate.registry.processors'),
                new Reference('run_open_code.exchange_rate.registry.rates')
            ));
        }
    }

    /**
     * Configure sources registry.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configureSourcesRegistry(array $config, ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate.registry.sources')) {

            $definition = $container->getDefinition('run_open_code.exchange_rate.registry.sources');
            $requiredSources = $this->getRequiredSources($config);


            foreach ($container->findTaggedServiceIds('run_open_code.exchange_rate.source') as $id => $tags) {

                foreach ($tags as $attributes) {

                    if (array_key_exists($attributes['alias'], $requiredSources)) {

                        $definition->addMethodCall('add', array(
                            new Reference($id)
                        ));

                        unset($requiredSources[$attributes['alias']]);
                    }
                }
            }

            if (count($requiredSources) > 0) {
                throw new InvalidConfigurationException(sprintf('Required source(s) "%s" does not exists.', implode(', ', $requiredSources)));
            }
        }
    }

    /**
     * Configure processors registry.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configureProcessorsRegistry(array $config, ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate.registry.processors')) {

            $definition = $container->getDefinition('run_open_code.exchange_rate.registry.processors');

            $processors = array();

            foreach ($container->findTaggedServiceIds('run_open_code.exchange_rate.processor') as $id => $tags) {

                if (!in_array($id, $config['processors'], true)) {
                    continue;
                }

                foreach ($tags as $attributes) {
                    $processors[$id] = (isset($attributes['priority'])) ? intval($attributes['priority']) : 0;
                }
            }

            asort($processors);

            foreach ($processors as $id => $processor) {
                $definition->addMethodCall('add', array(
                    new Reference($id)
                ));
            }
        }
    }

    /**
     * Configure rates.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configureRatesRegistry(array $config, ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate.registry.rates')) {

            $definition = $container->getDefinition('run_open_code.exchange_rate.registry.rates');

            $aliases = array();

            /**
             * @var Configuration $rateConfiguration
             */
            foreach ($config['rates'] as $rateConfiguration) {

                if ($rateConfiguration->getAlias() === null) {
                    continue;
                }

                if (array_key_exists($rateConfiguration->getAlias(), $aliases)) {
                    throw new InvalidConfigurationException(sprintf('Rate with alias "%s" is already defined.', $rateConfiguration->getAlias()));
                }

                $aliases[$rateConfiguration->getAlias()] = $rateConfiguration;
            }

            $definition->setArguments(array(array_values($config['rates'])));
        }
    }

    /**
     * Configure file registry, if used.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configureFileRepository(array $config, ContainerBuilder $container)
    {
        if (
            $config['repository'] === 'run_open_code.exchange_rate.repository.file_repository'
            &&
            $container->hasDefinition('run_open_code.exchange_rate.repository.file_repository')
        ) {

            if (!empty($config['file_repository']) && !empty($config['file_repository']['path'])) {
                $definition = $container->getDefinition('run_open_code.exchange_rate.repository.file_repository');
                $definition->setArguments(array(
                    $config['file_repository']['path']
                ));
            } else {
                throw new InvalidConfigurationException('You must configure location to the file where file repository will store exchange rates.');
            }

        } elseif ($config['repository'] === 'run_open_code.exchange_rate.repository.file_repository') {
            throw new InvalidConfigurationException('File repository is used to store exchange rates, but it is not available in container.');
        } else {
            $container->removeDefinition('run_open_code.exchange_rate.repository.file_repository');
        }
    }

    /**
     * Configure controller - view layer.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configureController(array $config, ContainerBuilder $container)
    {
        if ($container->has('run_open_code.exchange_rate.controller')) {
            $definition = $container->getDefinition('run_open_code.exchange_rate.controller');
            $definition->setArguments(array(
                new Reference($config['repository']),
                $config['base_currency'],
                $config['view']
            ));
        }
    }

    /**
     * Configure debug command.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configureDebugCommand(array $config, ContainerBuilder $container)
    {
        if ($container->has('run_open_code.exchange_rate.command.configuration_debug')) {
            $definition = $container->getDefinition('run_open_code.exchange_rate.command.configuration_debug');

            $arguments = $definition->getArguments();
            $arguments[3] = new Reference($config['repository']);

            $definition->setArguments($arguments);
        }
    }

    /**
     * Extract name of only required sources from configuration.
     *
     * @param array $config
     * @return array
     */
    protected function getRequiredSources(array $config)
    {
        $requiredSources = array();

        foreach ($config['rates'] as $rate) {
            $requiredSources[$rate['source']] = $rate['source'];
        }

        return $requiredSources;
    }
}
