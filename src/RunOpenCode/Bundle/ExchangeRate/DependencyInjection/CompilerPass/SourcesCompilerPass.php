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
        if ($container->hasDefinition('runopencode.exchange_rate.registry.sources')) {

            $definition = $container->getDefinition('runopencode.exchange_rate.registry.sources');

            foreach ($container->findTaggedServiceIds('runopencode.exchange_rate.source') as $id => $tags) {
                $definition->addMethodCall('add', [new Reference($id)]);
            }
        }
    }
}
