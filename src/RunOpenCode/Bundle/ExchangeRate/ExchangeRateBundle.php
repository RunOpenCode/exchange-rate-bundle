<?php

namespace RunOpenCode\Bundle\ExchangeRate;

use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ExchangeRateBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new Extension();
    }
}