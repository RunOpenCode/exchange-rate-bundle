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
 * Class FetchSuccessEvent
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Event
 */
class FetchSuccessEvent extends Event implements \IteratorAggregate, \ArrayAccess
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

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->rates);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->rates[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->rates[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new LogicException(sprintf('Method "%s" of class "%s" can not be invoked in this context.', __FUNCTION__, __CLASS__));
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new LogicException(sprintf('Method "%s" of class "%s" can not be invoked in this context.', __FUNCTION__, __CLASS__));
    }
}
