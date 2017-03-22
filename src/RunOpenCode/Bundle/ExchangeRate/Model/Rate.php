<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Model;

use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Model\Rate as BaseRate;

/**
 * Class Rate
 *
 * Extended rate with setters, adjusted for form.
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Model
 */
class Rate extends BaseRate
{
    /**
     * Set source name.
     *
     * @param string $sourceName
     * @return Rate Fluent interface.
     */
    public function setSourceName($sourceName)
    {
        $this->sourceName = $sourceName;
        return $this;
    }

    /**
     * Set value.
     *
     * @param float $value
     * @return Rate Fluent interface.
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set currency code.
     *
     * @param string $currencyCode
     * @return Rate Fluent interface.
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    /**
     * Set rate type.
     *
     * @param string $rateType
     * @return Rate Fluent interface.
     */
    public function setRateType($rateType)
    {
        $this->rateType = $rateType;
        return $this;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     * @return Rate Fluent interface.
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Set base currency code.
     *
     * @param string $baseCurrencyCode
     * @return Rate Fluent interface.
     */
    public function setBaseCurrencyCode($baseCurrencyCode)
    {
        $this->baseCurrencyCode = $baseCurrencyCode;
        return $this;
    }

    /**
     * Set created date.
     *
     * @param \DateTime $createdAt
     * @return Rate Fluent interface.
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Set last modification date.
     *
     * @param \DateTime $modifiedAt
     * @return Rate Fluent interface.
     */
    public function setModifiedAt(\DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
        return $this;
    }

    /**
     * Build this rate instance from any RateInterface implementation.
     *
     * @param RateInterface $rate
     * @return Rate
     */
    public static function fromRateInterface(RateInterface $rate)
    {
        return new static(
            $rate->getSourceName(),
            $rate->getValue(),
            $rate->getCurrencyCode(),
            $rate->getRateType(),
            $rate->getDate(),
            $rate->getBaseCurrencyCode(),
            $rate->getCreatedAt(),
            $rate->getModifiedAt()
        );
    }
}
