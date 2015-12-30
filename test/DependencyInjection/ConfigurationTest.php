<?php

namespace RunOpenCode\Bundle\ExchangeRate\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Configuration;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Extension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * @test
     */
    public function validConfiguration()
    {
        $expected = array(
            'base_currency' => 'RSD',
            'processors' => array(
                'run_open_code.exchange_rate.processor.base_currency_validator'
            ),
            'rates' => array(
                array('currency_code' => 'EUR', 'rate_type' => 'default', 'source' => 'national_bank_of_serbia'),
                array('currency_code' => 'CHF', 'rate_type' => 'default', 'source' => 'national_bank_of_serbia'),
                array('currency_code' => 'BAM', 'rate_type' => 'default', 'source' => 'national_bank_of_serbia')
            ),
            'file_repository' => array('path' => '/path/to/some/file'),
            'repository' => 'run_open_code.exchange_rate.repository.file_repository'
        );

        $sources = array(
            __DIR__ . '/../Fixtures/valid_configuration.yml'
        );

        $this->assertProcessedConfigurationEquals($expected, $sources);
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
