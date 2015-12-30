<?php

namespace RunOpenCode\Bundle\ExchangeRate\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function serviceIsReady()
    {
        $this->load(array(
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
        ));

        $this->assertContainerBuilderHasService('run_open_code.exchange_rate');


    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return array(
            new Extension()
        );
    }
}