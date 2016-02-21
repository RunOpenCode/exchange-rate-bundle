<?php

namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SourceTypeCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate.form_type.source_type')) {

            $container
                ->getDefinition('run_open_code.exchange_rate.form_type.source_type')
                ->setArguments(array(
                    $container->getParameter('run_open_code.exchange_rate.form_type.source_type')
                ));
        }
    }
}