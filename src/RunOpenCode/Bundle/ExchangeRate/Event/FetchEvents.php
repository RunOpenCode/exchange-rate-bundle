<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Event;

/**
 * Class FetchEvent
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Event
 */
final class FetchEvents
{
    /**
     * Rates are successfully fetched.
     */
    const SUCCESS = 'runopencode.exchange_rate.fetch.success';

    /**
     * There has been an error when fetching rates.
     */
    const ERROR = 'runopencode.exchange_rate.fetch.error';

    private function __construct()
    {
        // noop
    }
}
