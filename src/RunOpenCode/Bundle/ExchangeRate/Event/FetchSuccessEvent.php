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

use Symfony\Component\EventDispatcher\Event;

/**
 * Class FetchSuccessEvent
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Event
 */
class FetchSuccessEvent extends Event
{
    /**
     * @var array
     */
    private $rates;

    /**
     * @var \DateTime
     */
    private $date;

    public function __construct(array $rates, \DateTime $date)
    {
        $this->rates = $rates;
        $this->date = $date;
    }

    /**
     * Get fetched rates.
     *
     * @return array
     */
    public function getRates()
    {
        return $this->rates;
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
