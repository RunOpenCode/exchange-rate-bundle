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
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\SourcesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SourcesCompilerPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $rateConfiguration1 = new Definition();
        $rateConfiguration1
            ->setArguments(['currency_code', 'rate_type', 'source_1'])
            ->addTag('run_open_code.exchange_rate.rate_configuration');

        $this->setDefinition('source_1_service', $rateConfiguration1);

        $rateConfiguration2 = new Definition();
        $rateConfiguration2
            ->setArguments(['currency_code', 'rate_type', 'source_2'])
            ->addTag('run_open_code.exchange_rate.rate_configuration');

        $this->setDefinition('source_2_service', $rateConfiguration2);
    }

    /**
     * @test
     */
    public function itRegistersRequiredSourcesIntoSourceRegistry()
    {
        $this->setDefinition('run_open_code.exchange_rate.registry.sources', new Definition());

        $source1 = new Definition();
        $source1
            ->addTag('run_open_code.exchange_rate.source', ['name' => 'source_1']);

        $source2 = new Definition();
        $source2
            ->addTag('run_open_code.exchange_rate.source', ['name' => 'source_2']);

        $source3 = new Definition();
        $source3
            ->addTag('run_open_code.exchange_rate.source', ['name' => 'source_3']);

        $this->setDefinition('source_service_1', $source1);
        $this->setDefinition('source_service_2', $source2);
        $this->setDefinition('source_service_3', $source3);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('run_open_code.exchange_rate.registry.sources', 'add', [new Reference('source_service_1')]);
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('run_open_code.exchange_rate.registry.sources', 'add', [new Reference('source_service_2')]);
        $this->assertEquals(2, count($this->container->findDefinition('run_open_code.exchange_rate.registry.sources')->getMethodCalls()));
    }

    /**
     * @test
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function itThrowsExceptionWhenSourceIsMissing()
    {
        $this->setDefinition('run_open_code.exchange_rate.registry.sources', new Definition());

        $source1 = new Definition();
        $source1
            ->addTag('run_open_code.exchange_rate.source', ['name' => 'source_1']);

        $source3 = new Definition();
        $source3
            ->addTag('run_open_code.exchange_rate.source', ['name' => 'source_3']);

        $this->setDefinition('source_service_1', $source1);
        $this->setDefinition('source_service_3', $source3);

        $this->compile();
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new SourcesCompilerPass());
    }
}
