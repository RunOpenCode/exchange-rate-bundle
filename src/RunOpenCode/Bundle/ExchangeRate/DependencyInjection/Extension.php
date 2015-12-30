<?php

namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection;

use RunOpenCode\ExchangeRate\Configuration as RateConfiguration;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class Extension extends BaseExtension
{
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
    }

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

    protected function configureSourcesRegistry(array $config, ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate.registry.sources')) {

            $definition = $container->getDefinition('run_open_code.exchange_rate.registry.sources');

            $requiredSources = array();

            foreach ($config['rates'] as $rate) {
                $requiredSources[$rate['source']] = $rate['source'];
            }

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

    protected function configureProcessorsRegistry(array $config, ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate.registry.processors')) {

            $definition = $container->getDefinition('run_open_code.exchange_rate.registry.processors');

            $processors = array();

            foreach ($container->findTaggedServiceIds('run_open_code.exchange_rate.processors') as $id => $tags) {

                if (!isset($config['processors'][$id])) {
                    continue;
                }

                foreach ($tags as $attributes) {
                    $processors[$id] = (isset($attributes['priority'])) ? intval($attributes['priority']) : 0;
                }
            }

            asort($processors);

            foreach (array_keys($processors) as $id) {
                $definition->addMethodCall('add', array(
                    new Definition($id)
                ));
            }
        }
    }

    protected function configureRatesRegistry(array $config, ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate.registry.rates')) {

            $definition = $container->getDefinition('run_open_code.exchange_rate.registry.rates');

            $processed = array();

            foreach ($config['rates'] as $rateConfiguration) {

                $key = sprintf('%s_%s', $rateConfiguration['currency_code'], $rateConfiguration['rate_type']);

                if (isset($processed[$key])) {
                    throw new InvalidConfigurationException(sprintf('Currency code "%s" for rate type "%s" is configured twice.', $rateConfiguration['currency_code'], $rateConfiguration['rate_type']));
                }

                $rateConfiguration['extra'] = (isset($ratesConfiguration['extra'])) ? $ratesConfiguration['extra'] : array();

                $processed[$key] = $rateConfiguration;
            }

            $definition->setArguments(array(
                array_values($processed)
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return "run_open_code_exchange_rate";
    }
}
