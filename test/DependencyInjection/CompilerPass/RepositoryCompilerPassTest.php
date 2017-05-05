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
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\CompilerPass\RepositoryCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RepositoryCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function itRegistersServiceAsRepository()
    {
        $this->setDefinition('some_dummy_repository', new Definition('DummyClass'));
        $this->setParameter('runopencode.exchange_rate.repository', 'some_dummy_repository');

        $this->compile();

        $this->assertContainerBuilderHasService('runopencode.exchange_rate.repository', 'DummyClass');
    }

    /**
     * @test
     */
    public function itRegistersTaggedServiceAsRepository()
    {
        $definition = new Definition('DummyClass');
        $definition
            ->addTag('runopencode.exchange_rate.repository', ['alias' => 'repository_service']);

        $this->setDefinition('some_dummy_repository', $definition);

        $this->setParameter('runopencode.exchange_rate.repository', 'repository_service');

        $this->compile();

        $this->assertContainerBuilderHasService('runopencode.exchange_rate.repository', 'DummyClass');
    }

    /**
     * @test
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function itReportsMissingRepositoryService()
    {
        $this->setParameter('runopencode.exchange_rate.repository', 'missing_service');

        $this->compile();
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new RepositoryCompilerPass());
    }
}
