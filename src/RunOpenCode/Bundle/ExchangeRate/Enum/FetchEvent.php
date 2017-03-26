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
 * Class FetchEvent
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Enum
 */
final class FetchEvent
{
    /**
     * Rates are successfully fetched.
     */
    const SUCCESS = 'success';

    /**
     * There has been an error when fetching rates.
     */
    const ERROR = 'error';

    private function __construct()
    {
        // noop
    }
}
