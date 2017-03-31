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
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\ProcessorsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ProcessorsCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function itRegistersProcessors()
    {
        $this->setDefinition('run_open_code.exchange_rate.registry.processors', new Definition());

        $processor1 = new Definition();
        $processor1
            ->addTag('run_open_code.exchange_rate.processor', [ 'priority' => 10 ]);

        $processor2 = new Definition();
        $processor2
            ->addTag('run_open_code.exchange_rate.processor', [ 'priority' => 20 ]);

        $processor3 = new Definition();
        $processor3
            ->addTag('run_open_code.exchange_rate.processor', []);

        $processor4 = new Definition();
        $processor4
            ->addTag('run_open_code.exchange_rate.processor', [ 'priority' => -5 ]);

        $this->setDefinition('processor1', $processor1);
        $this->setDefinition('processor2', $processor2);
        $this->setDefinition('processor3', $processor3);
        $this->setDefinition('processor4', $processor4);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('run_open_code.exchange_rate.registry.processors', 'add', [new Reference('processor4')], 0);
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('run_open_code.exchange_rate.registry.processors', 'add', [new Reference('processor3')], 1);
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('run_open_code.exchange_rate.registry.processors', 'add', [new Reference('processor1')], 2);
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('run_open_code.exchange_rate.registry.processors', 'add', [new Reference('processor2')], 3);
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new ProcessorsCompilerPass());
    }
}
