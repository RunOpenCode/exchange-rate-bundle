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

use RunOpenCode\Bundle\ExchangeRate\Exception\LogicException;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class FetchErrorEvent
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Event
 */
class FetchErrorEvent extends Event
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @var \DateTime
     */
    private $date;

    public function __construct(array $errors, \DateTime $date)
    {
        $this->errors = $errors;
        $this->date = $date;
    }

    /**
     * Get errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get date for which rates are fetched.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
