<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\Fixtures\Source;

use Psr\Log\LoggerAwareTrait;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Contract\SourceInterface;
use RunOpenCode\ExchangeRate\Enum\RateType;
use RunOpenCode\ExchangeRate\Exception\ConfigurationException;
use RunOpenCode\ExchangeRate\Exception\SourceNotAvailableException;
use RunOpenCode\ExchangeRate\Exception\UnknownCurrencyCodeException;
use RunOpenCode\ExchangeRate\Exception\UnknownRateTypeException;

class DummySource implements SourceInterface
{
    use LoggerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dummy_source';
    }

    /**
     * Fetch rate value from source.
     *
     * @param string $currencyCode ISO currency code for which rate is being fetched.
     * @param string $rateType Type of the rate which is being fetched.
     * @param null|\DateTime $date Date on which rate is being fetched.
     *
     * @return RateInterface Fetched rate.
     *
     * @throws UnknownCurrencyCodeException
     * @throws UnknownRateTypeException
     * @throws SourceNotAvailableException
     * @throws ConfigurationException
     */
    public function fetch($currencyCode, $rateType = RateType::MEDIAN, \DateTime $date = null)
    {
        // TODO: Implement fetch() method.
    }
}
