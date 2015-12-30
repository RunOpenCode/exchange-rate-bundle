<?php

namespace RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Factory;

use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Registry\RatesConfigurationRegistry;

final class RatesConfigurationRegistryFactory
{
    private function __construct() { }

    public static function build(array $ratesConfigurations)
    {
        $registry = new RatesConfigurationRegistry();

        foreach ($ratesConfigurations as $ratesConfiguration) {
            $registry->add(new Configuration(
                $ratesConfiguration['currency_code'],
                $ratesConfiguration['rate_type'],
                $ratesConfiguration['source'],
                $ratesConfiguration['extra']
            ));
        }

        return $registry;
    }
}