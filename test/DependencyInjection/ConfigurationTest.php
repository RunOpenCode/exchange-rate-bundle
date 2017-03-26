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

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * @test
     */
    public function test()
    {
        $this->assertTrue(true);
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
