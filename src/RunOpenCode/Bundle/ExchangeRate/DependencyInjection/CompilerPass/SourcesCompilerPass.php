<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SourcesCompilerPass
 *
 * Compiler pass for sources
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

            foreach ($container->findTaggedServiceIds('run_open_code.exchange_rate.source') as $id => $tags) {

                foreach ($tags as $attributes) {

                    if (
                        !empty($attributes['name'])
                        &&
                        isset($requiredSources[$attributes['name']])
                    ) {
                        $definition->addMethodCall('add', [new Reference($id)]);
                        unset($requiredSources[$attributes['name']]);

                        continue 2;
                    }
                }
            }

            if (count($requiredSources) > 0) {
                throw new ServiceNotFoundException(reset($requiredSources));
            }
        }
    }

    /**
     * Get list of required sources services.
     *
     * @param ContainerBuilder $container
     * @return array
     */
    protected function getRequiredSources(ContainerBuilder $container)
    {
        $sources = [];

        foreach ($container->findTaggedServiceIds('run_open_code.exchange_rate.rate_configuration') as $id => $tags) {
            $source = $container->getDefinition($id)->getArgument(2);
            $sources[$source] = $source;
        }

        return $sources;
    }
}
