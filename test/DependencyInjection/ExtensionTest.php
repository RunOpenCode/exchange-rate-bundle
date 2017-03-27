<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Extension;
use RunOpenCode\Bundle\ExchangeRate\Enum\Role;
use RunOpenCode\Bundle\ExchangeRate\Security\AccessVoter;
use RunOpenCode\ExchangeRate\Configuration;
use Symfony\Component\DependencyInjection\Reference;

class ExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function itConfiguresBaseCurrency()
    {
        $this->load([
            'base_currency' => 'RSD',
            'rates' => [
                ['currency_code' => 'EUR', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia'],
            ]
        ]);

        $this->assertContainerBuilderHasParameter('run_open_code.exchange_rate.base_currency', 'RSD');
    }

    /**
     * @test
     */
    public function itConfiguresRates()
    {
        $rates = [
            ['currency_code' => 'EUR', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia'],
            ['currency_code' => 'CHF', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia'],
            ['currency_code' => 'USD', 'rate_type' => 'selling', 'source' => 'bloomberg'],
        ];

        $this->load([
            'base_currency' => 'RSD',
            'rates' => $rates
        ]);

        $services = $this->container->findTaggedServiceIds('run_open_code.exchange_rate.rate_configuration');

        $this->assertEquals(3, count($services));

        $configured = [];

        foreach ($services as $id => $tags) {

            $definition = $this->container->findDefinition($id);
            $arguments = $definition->getArguments();

            $this->assertEquals($definition->getClass(), Configuration::class);

            $configured[] = [
                'currency_code' => $arguments[0],
                'rate_type' => $arguments[1],
                'source' => $arguments[2]
            ];
        }

        $this->assertEquals($rates, $configured);
    }

    /**
     * @test
     */
    public function itConfiguresSources()
    {
        $sources = [
            'simple_source_1' => '\Simple\SourceClass1',
            'simple_source_2' => '\Simple\SourceClass2',
        ];

        $this->load([
            'base_currency' => 'RSD',
            'rates' => [
                ['currency_code' => 'EUR', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia'],
            ],
            'sources' => $sources
        ]);

        $services = $this->container->findTaggedServiceIds('run_open_code.exchange_rate.source');

        $this->assertEquals(2, count($services));

        $configured = [];

        foreach ($services as $id => $tags) {
            $definition = $this->container->findDefinition($id);
            $configured[$tags[0]['alias']] = $definition->getClass();
        }

        $this->assertEquals($sources, $configured);
    }

    /**
     * @test
     */
    public function itConfiguresRepository()
    {
        $this->load([
            'base_currency' => 'RSD',
            'rates' => [
                ['currency_code' => 'EUR', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia'],
            ],
            'repository' => 'file'
        ]);

        $this->assertContainerBuilderHasParameter('run_open_code.exchange_rate.repository', 'file');
    }

    /**
     * @test
     */
    public function itConfiguresFileRepository()
    {
        $this->load([
            'base_currency' => 'RSD',
            'rates' => [
                ['currency_code' => 'EUR', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia'],
            ],
            'file_repository' => [
                'path' => 'path/to/file'
            ]
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('run_open_code.exchange_rate.repository.file_repository', 0, 'path/to/file');
    }

    /**
     * @test
     */
    public function itConfiguresDoctrineDbalRepository()
    {
        $this->load([
            'base_currency' => 'RSD',
            'rates' => [
                ['currency_code' => 'EUR', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia'],
            ],
            'doctrine_dbal_repository' => [
                'table_name' => 'table_name',
                'connection' => 'connection'
            ]
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('run_open_code.exchange_rate.repository.doctrine_dbal_repository', 0, new Reference('connection'));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('run_open_code.exchange_rate.repository.doctrine_dbal_repository', 1, 'table_name');
    }

    /**
     * @test
     */
    public function itDisablesAccessVoter()
    {
        $this->load([
            'base_currency' => 'RSD',
            'rates' => [
                ['currency_code' => 'EUR', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia'],
            ],
            'security' => [
                'enabled' => false,
            ]
        ]);

        $this->assertContainerBuilderNotHasService('run_open_code.exchange_rate.security.access_voter');
    }

    /**
     * @test
     */
    public function itConfiguresAccessVoter()
    {
        $roles = [
            AccessVoter::VIEW => [ Role::MANAGE_RATE ],
            AccessVoter::EDIT => [ Role::MANAGE_RATE ],
            AccessVoter::DELETE => [ Role::MANAGE_RATE ],
            AccessVoter::CREATE => [ Role::MANAGE_RATE ],
        ];

        $this->load([
            'base_currency' => 'RSD',
            'rates' => [
                ['currency_code' => 'EUR', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia'],
            ],
            'security' => $roles
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('run_open_code.exchange_rate.security.access_voter', 0, $roles);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [new Extension()];
    }
}
