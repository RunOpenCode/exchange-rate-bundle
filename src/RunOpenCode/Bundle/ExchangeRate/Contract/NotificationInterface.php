<?php

namespace RunOpenCode\Bundle\ExchangeRate\Contract;

interface NotificationInterface
{
    /**
     * Send internal notification about event.
     *
     * @param array $vars Associative array of variables useful for rendering notification.
     */
    public function notify(array $vars = array());
}