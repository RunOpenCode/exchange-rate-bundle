<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate;

use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\ProcessorsCompilerPass;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\RatesConfigurationRegistryCompilerPass;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\RepositoryCompilerPass;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\SourcesCompilerPass;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ExchangeRateBundle
 *
 * A Bundle.
 *
 * @package RunOpenCode\Bundle\ExchangeRate
 */
class ExchangeRateBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new Extension();
    }

    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new RepositoryCompilerPass())
            ->addCompilerPass(new SourcesCompilerPass())
            ->addCompilerPass(new ProcessorsCompilerPass())
            ->addCompilerPass(new RatesConfigurationRegistryCompilerPass());
    }
}
