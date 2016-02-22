<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RateTypeCompilerPass
 *
 * Compiler pass for configuring defaults for RunOpenCode\Bundle\ExchangeRate\Form\Type\RateType
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass
 */
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
