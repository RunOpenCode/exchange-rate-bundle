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

/**
 * Class CurrencyCodeTypeCompilerPass
 *
 * Compiler pass for configuring defaults for RunOpenCode\Bundle\ExchangeRate\Form\Type\CurrencyCodeType
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass
 */
class CurrencyCodeTypeCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('run_open_code.exchange_rate.form_type.currency_code_type')) {

            $container
                ->getDefinition('run_open_code.exchange_rate.form_type.currency_code_type')
                ->setArguments(array(
                    $container->getParameter('run_open_code.exchange_rate.form_type.currency_code_type')
                ));
        }
    }
}
