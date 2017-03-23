<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Enum;

/**
 * Class NotifyingEvent
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Enum
 */
final class NotifyingEvent
{
    /**
     * Rates are successfully fetched.
     */
    const FETCH_RATES_SUCCESS = 'fetch_rates_success';

    /**
     * There has been an error when fetching rates.
     */
    const FETCH_RATES_ERROR = 'fetch_rates_error';

    private function __construct()
    {
        // noop
    }
}
