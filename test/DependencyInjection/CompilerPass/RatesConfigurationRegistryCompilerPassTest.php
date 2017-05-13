<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\DependencyInjection\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\RatesConfigurationRegistryCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RatesConfigurationRegistryCompilerPassTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\DependencyInjection\CompilerPass
 */
class RatesConfigurationRegistryCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function itConfiguresRatesConfigurationRegistry()
    {
        $this->setDefinition('runopencode.exchange_rate.registry.rates', new Definition());

        $rateConfiguration1 = new Definition();
        $rateConfiguration1
            ->addTag('runopencode.exchange_rate.rate_configuration');

        $rateConfiguration2 = new Definition();
        $rateConfiguration2
            ->addTag('runopencode.exchange_rate.rate_configuration');

        $rateConfiguration3 = new Definition();
        $rateConfiguration3
            ->addTag('runopencode.exchange_rate.rate_configuration');

        $this->setDefinition('rate_configuration_1', $rateConfiguration1);
        $this->setDefinition('rate_configuration_2', $rateConfiguration2);
        $this->setDefinition('rate_configuration_3', $rateConfiguration3);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('runopencode.exchange_rate.registry.rates', 'add', [new Reference('rate_configuration_1')]);
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('runopencode.exchange_rate.registry.rates', 'add', [new Reference('rate_configuration_2')]);
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('runopencode.exchange_rate.registry.rates', 'add', [new Reference('rate_configuration_3')]);
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new RatesConfigurationRegistryCompilerPass());
    }
}
