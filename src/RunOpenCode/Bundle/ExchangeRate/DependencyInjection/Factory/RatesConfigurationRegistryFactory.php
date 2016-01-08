<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Factory;

use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Registry\RatesConfigurationRegistry;

/**
 * Class RatesConfigurationRegistryFactory
 *
 * Rate configuration builder.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Factory
 */
final class RatesConfigurationRegistryFactory
{
    private function __construct() { }

    /**
     * Build rate configuration from array.
     *
     * @param array $ratesConfigurations
     * @return RatesConfigurationRegistry
     */
    public static function build(array $ratesConfigurations)
    {
        $registry = new RatesConfigurationRegistry();

        foreach ($ratesConfigurations as $ratesConfiguration) {
            $registry->add(new Configuration(
                $ratesConfiguration['currency_code'],
                $ratesConfiguration['rate_type'],
                $ratesConfiguration['source'],
                $ratesConfiguration['alias'],
                !empty($ratesConfiguration['extra']) ? $ratesConfiguration['extra'] : array()
            ));
        }

        return $registry;
    }
}
