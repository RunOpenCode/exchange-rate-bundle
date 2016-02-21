<?php

namespace RunOpenCode\Bundle\ExchangeRate\Registry;

use RunOpenCode\ExchangeRate\Configuration;
use RunOpenCode\ExchangeRate\Contract\RatesConfigurationRegistryInterface;
use RunOpenCode\ExchangeRate\Registry\RatesConfigurationRegistry as BaseRatesConfigurationRegistry;

final class RatesConfigurationRegistry implements RatesConfigurationRegistryInterface
{
    private $registry;

    public function __construct(array $registeredRates)
    {
        $this->registry = new BaseRatesConfigurationRegistry();

        foreach ($registeredRates as $registeredRate) {
            $this->add(new Configuration(
                $registeredRate['currency_code'],
                $registeredRate['rate_type'],
                $registeredRate['source'],
                !empty($registeredRate['extra']) ? $registeredRate['extra'] : array()
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->registry->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function add(Configuration $configuration)
    {
        $this->registry->add($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $filter = array())
    {
        return $this->registry->all($filter);
    }
}
