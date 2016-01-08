<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2016 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate;

use RunOpenCode\Bundle\ExchangeRate\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ExchangeRateBundle
 *
 * A Bundle.
 *
 * @package RunOpenCode\Bundle\ExchangeRate
 */
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
