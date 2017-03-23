<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate;

/**
 * Class Role
 *
 * @package RunOpenCode\Bundle\ExchangeRate
 */
final class Role
{
    /**
     * Manage all rates and execute any operation against rates
     */
    const MANAGE_RATE = 'ROLE_EXCHANGE_RATE_MANAGE';

    /**
     * See rates
     */
    const VIEW_RATE = 'ROLE_EXCHANGE_RATE_VIEW';

    /**
     * Create rate
     */
    const CREATE_RATE = 'ROLE_EXCHANGE_RATE_CREATE';

    /**
     * Edit rate
     */
    const EDIT_RATE = 'ROLE_EXCHANGE_RATE_EDIT';

    /**
     * Delete rate
     */
    const DELETE_RATE = 'ROLE_EXCHANGE_RATE_DELETE';

    private function __construct()
    {
        // noop
    }
}
