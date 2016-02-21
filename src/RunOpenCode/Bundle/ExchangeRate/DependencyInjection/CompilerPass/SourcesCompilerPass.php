<?php

namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SourcesCompilerPass
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass
 */
class SourcesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate.registry.sources')) {

            $definition = $container->getDefinition('run_open_code.exchange_rate.registry.sources');

            $requiredSources = $this->getRequiredSources($container);

            $availableSources = array_merge(
                $this->getRegisteredContainerSources($container),
                $this->getRegisteredSimpleSources($container)
            );

            if (count($availableSources) === 0) {
                throw new \RuntimeException('There is no available exchange rate source service registered in Service Container.');
            }


            foreach ($requiredSources as $requiredSource) {

                if (array_key_exists($requiredSource, $availableSources)) {

                    $definition->addMethodCall('add', array(
                        new Reference($availableSources[$requiredSource])
                    ));
                } else {
                    throw new \RuntimeException(sprintf('Required source "%s" is not available in Service Container.', $requiredSource));
                }
            }
        }
    }

    protected function getRequiredSources(ContainerBuilder $container)
    {
        $rates = $container->getParameter('run_open_code.exchange_rate.registered_rates');

        return array_unique(array_map(function($rateConfiguration) {
            return $rateConfiguration['source'];
        }, $rates));
    }

    protected function getRegisteredContainerSources(ContainerBuilder $container)
    {
        $availableSources = array();

        foreach ($container->findTaggedServiceIds('run_open_code.exchange_rate.source') as $id => $tags) {

            foreach ($tags as $attributes) {

                if (!empty($attributes['alias'])) {
                    $availableSources[$attributes['alias']] = $id;
                    continue;
                }
            }
        }

        return $availableSources;
    }

    protected function getRegisteredSimpleSources(ContainerBuilder $container)
    {
        /**
         * @var array $sources
         */
        if ($container->hasParameter('run_open_code.exchange_rate.source.registered_simple_sources')) {

            $availableSources = array();

            foreach ($container->getParameter('run_open_code.exchange_rate.source.registered_simple_sources') as $key => $class) {

                $definition = new Definition($class);

                $definition
                    ->setPublic(false)
                    ->addTag('run_open_code.exchange_rate.source', array(
                        'alias' => $key
                    ));

                $container->setDefinition(($id = sprintf('run_open_code.exchange_rate.source.simple.%s', $key)), $definition);

                $availableSources[$key] = $id;
            }

            return $availableSources;
        }

        return array();
    }
}