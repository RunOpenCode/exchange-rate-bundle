<?php

namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RateTypeCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate.form_type.rate_type')) {

            $container
                ->getDefinition('run_open_code.exchange_rate.form_type.rate_type')
                ->setArguments(array(
                    new Reference('run_open_code.exchange_rate.registry.rates'),
                    new Reference('translator'),
                    $container->getParameter('run_open_code.exchange_rate.form_type.rate_type')
                ));
        }
    }
}
