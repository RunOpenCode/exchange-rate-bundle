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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Configuration;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Extension;
use RunOpenCode\Bundle\ExchangeRate\Enum\Role;
use RunOpenCode\Bundle\ExchangeRate\Security\AccessVoter;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * @test
     */
    public function itHasReasonableDefaults()
    {
        $this->assertProcessedConfigurationEquals([
            'base_currency' => 'RSD',
            'repository' => 'file',
            'rates' => [
                ['currency_code' => 'EUR', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia', 'extra' => ['key' => 'Value', 'otherKey' => 'OtherValue']],
                ['currency_code' => 'CHF', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia', 'extra' => []],
                ['currency_code' => 'USD', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia', 'extra' => []],
            ],
            'file_repository' => [
                'path' => '%kernel.root_dir%/../var/db/exchange_rates.dat',
            ],
            'doctrine_dbal_repository' => [
                'connection' => 'doctrine.dbal.default_connection',
                'table_name' => 'runopencode_exchange_rate',
            ],
            'sources' => [],
            'security' => [
                'enabled' => true,
                AccessVoter::VIEW => [Role::MANAGE_RATE, Role::VIEW_RATE],
                AccessVoter::CREATE => [Role::MANAGE_RATE, Role::CREATE_RATE],
                AccessVoter::EDIT => [Role::MANAGE_RATE, Role::EDIT_RATE],
                AccessVoter::DELETE => [Role::MANAGE_RATE, Role::DELETE_RATE],
            ],
            'form_types' => [
                'source_type' => [ 'choice_translation_domain' => 'runopencode_exchange_rate', 'preferred_choices' => [] ],
                'rate_type_type' => [ 'choice_translation_domain' => 'runopencode_exchange_rate', 'preferred_choices' => [] ],
                'currency_code_type' => [ 'choice_translation_domain' => 'runopencode_exchange_rate', 'preferred_choices' => [] ],
                'foreign_currency_code_type' => [ 'choice_translation_domain' => 'runopencode_exchange_rate', 'preferred_choices' => [] ],
                'rate_type' => [ 'choice_translation_domain' => 'runopencode_exchange_rate', 'preferred_choices' => [] ],
            ],
            'notifications' => [
                'e_mail' => [
                    'enabled' => false,
                    'recipients' => []
                ]
            ]
        ], [
            __DIR__.'/../Fixtures/config/minimum.xml'
        ]);
    }

    /**
     * @test
     */
    public function itCanBeFullyConfigured()
    {
        $expected = [
            'base_currency' => 'RSD',
            'repository' => 'doctrine_dbal',
            'rates' => [
                ['currency_code' => 'EUR', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia', 'extra' => ['key' => 'Value', 'otherKey' => 'OtherValue']],
                ['currency_code' => 'CHF', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia', 'extra' => []],
                ['currency_code' => 'USD', 'rate_type' => 'median', 'source' => 'national_bank_of_serbia', 'extra' => []],
            ],
            'file_repository' => [
                'path' => '/path/to/file.dat',
            ],
            'doctrine_dbal_repository' => [
                'connection' => 'default',
                'table_name' => 'table',
            ],
            'sources' => [
                'SomeClass'
            ],
            'security' => [
                'enabled' => false,
                AccessVoter::VIEW => ['List1', 'List2'],
                AccessVoter::CREATE => ['Create1', 'Create2'],
                AccessVoter::EDIT => ['Edit1', 'Edit2'],
                AccessVoter::DELETE => ['Delete1', 'Delete2'],
            ],
            'form_types' => [
                'source_type' => [ 'choice_translation_domain' => 'roc', 'preferred_choices' => ['opt1', 'opt2'] ],
                'rate_type_type' => [ 'choice_translation_domain' => 'roc', 'preferred_choices' => ['opt1', 'opt2'] ],
                'currency_code_type' => [ 'choice_translation_domain' => 'roc', 'preferred_choices' => ['opt1', 'opt2'] ],
                'foreign_currency_code_type' => [ 'choice_translation_domain' => 'roc', 'preferred_choices' => ['opt1', 'opt2'] ],
                'rate_type' => [ 'choice_translation_domain' => 'roc', 'preferred_choices' => ['opt1', 'opt2'] ],
            ],
            'notifications' => [
                'e_mail' => [
                    'enabled' => true,
                    'recipients' => ['test@test.com', 'other@test.com']
                ]
            ]
        ];

        $this->assertProcessedConfigurationEquals($expected, [
            __DIR__.'/../Fixtures/config/full.xml'
        ]);

        $this->assertProcessedConfigurationEquals($expected, [
            __DIR__.'/../Fixtures/config/full.yml'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtension()
    {
        return new Extension();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
