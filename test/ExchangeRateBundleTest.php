<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests;

use PHPUnit\Framework\TestCase;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\ProcessorsCompilerPass;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\RatesConfigurationRegistryCompilerPass;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\RepositoryCompilerPass;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\SourcesCompilerPass;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Extension;
use RunOpenCode\Bundle\ExchangeRate\ExchangeRateBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ExchangeRateBundleTest
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests
 */
class ExchangeRateBundleTest extends TestCase
{
    /**
     * @test
     */
    public function itGetsExtension()
    {
        $this->assertInstanceOf(Extension::class, (new ExchangeRateBundle())->getContainerExtension());
    }

    /**
     * @test
     */
    public function itRegistersCompilerPasses()
    {
        $container = $this->getMockBuilder(ContainerBuilder::class)->getMock();

        $container
            ->expects($spy = $this->exactly(4))
            ->method('addCompilerPass')
            ->willReturn($container);

        $bundle = new ExchangeRateBundle();

        $bundle
            ->build($container);

        $invocations = $spy->getInvocations();

        $registeredCompilerPasses = [];

        /**
         * @var \PHPUnit_Framework_MockObject_Invocation_Object $invocation
         */
        foreach ($invocations as $invocation) {
            $registeredCompilerPasses[] = get_class($invocation->parameters[0]);
        }

        $this->assertCount(4, $invocations);
        $this->assertEquals([
            RepositoryCompilerPass::class,
            SourcesCompilerPass::class,
            ProcessorsCompilerPass::class,
            RatesConfigurationRegistryCompilerPass::class
        ], $registeredCompilerPasses);
    }
}
