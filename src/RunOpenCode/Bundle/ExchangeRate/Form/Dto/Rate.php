<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Form\Dto;

use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Model\Rate as ExchangeRate;

/**
 * Class Rate
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Dto
 */
class Rate
{
    /**
     * @var string
     */
    private $rate;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var float
     */
    private $value;

    /**
     * @var string
     */
    private $baseCurrencyCode;

    /**
     * Rate constructor.
     *
     * @param null $rate
     * @param \DateTime|null $date
     * @param null $value
     */
    public function __construct($rate = null, \DateTime $date = null, $value = null, $baseCurrencyCode = null)
    {
        $this->rate = $rate;
        $this->date = $date;
        $this->value = $value;
        $this->baseCurrencyCode = $baseCurrencyCode;
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param string $rate
     * @return Rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Rate
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return Rate
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->baseCurrencyCode;
    }

    /**
     * @param string $baseCurrencyCode
     * @return Rate
     */
    public function setBaseCurrencyCode($baseCurrencyCode)
    {
        $this->baseCurrencyCode = $baseCurrencyCode;
        return $this;
    }

    /**
     * Build \RunOpenCode\ExchangeRate\Model\Rate from DTO object.
     *
     * @param ExchangeRate|null $exchangeRate Initial rate object that can be used for data population
     * @return ExchangeRate
     */
    public function toRate(ExchangeRate $exchangeRate = null)
    {
        list ($sourceName, $rateType, $currencyCode) = explode('.', $this->getRate());

        return new ExchangeRate(
            $sourceName, 
            $this->getValue(),
            $currencyCode,
            $rateType,
            $this->getDate(),
            $this->baseCurrencyCode,
            (null !== $exchangeRate) ? $exchangeRate->getCreatedAt() : new \DateTime('now'),
            new \DateTime('now')
        );
    }

    /**
     * Create DTO object from RateInterface.
     *
     * @param RateInterface $exchangeRate
     * @return Rate
     */
    public static function fromRateInterface(RateInterface $exchangeRate)
    {
        $rate = new static();

        $rate
            ->setBaseCurrencyCode($exchangeRate->getBaseCurrencyCode())
            ->setDate($exchangeRate->getDate())
            ->setValue($exchangeRate->getValue())
            ->setRate(sprintf('%s.%s.%s', $exchangeRate->getSourceName(), $exchangeRate->getRateType(), $exchangeRate->getCurrencyCode()));

        return $rate;
    }
}
