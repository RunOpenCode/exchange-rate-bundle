<?php

namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ProcessorsCompilerPass
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass
 */
class ProcessorsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate.registry.processors')) {

            $definition = $container->getDefinition('run_open_code.exchange_rate.registry.processors');

            $processors = array();

            foreach ($container->findTaggedServiceIds('run_open_code.exchange_rate.processor') as $id => $tags) {

                foreach ($tags as $attributes) {
                    $processors[$id] = !empty($attributes['priority']) ? (int)$attributes['priority'] : 0;
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
}
