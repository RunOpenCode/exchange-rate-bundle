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
 * Class RatesConfigurationRegistryCompilerPass
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass
 */
class RatesConfigurationRegistryCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {

        if (!$container->hasDefinition('runopencode.exchange_rate.registry.rates')) {
            return;
        }

        $registry = $container->findDefinition('runopencode.exchange_rate.registry.rates');

        foreach (array_keys($container->findTaggedServiceIds('runopencode.exchange_rate.rate_configuration')) as $id) {

            $registry
                ->addMethodCall('add', [new Reference($id)]);
        }
    }
}
