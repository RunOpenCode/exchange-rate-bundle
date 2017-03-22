<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Contract;

/**
 * Interface NotificationInterface
 *
 * Internal notification system.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Contract
 */
interface NotificationInterface
{
    /**
     * Send internal notification about event.
     *
     * @param array $vars Associative array of variables useful for rendering notification.
     */
    public function notify(array $vars = array());
}
