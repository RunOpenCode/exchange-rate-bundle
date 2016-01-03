<?php

namespace RunOpenCode\Bundle\ExchangeRate\Model;

use RunOpenCode\ExchangeRate\Model\Rate as BaseRate;
use RunOpenCode\ExchangeRate\Utils\CurrencyCode;

class Rate extends BaseRate
{
    /**
     * Set source name.
     *
     * @param string $sourceName
     */
    public function setSourceName($sourceName)
    {
        $this->sourceName = $sourceName;
    }

    /**
     * Set value.
     *
     * @param float $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Set currency code.
     *
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = CurrencyCode::validate($currencyCode);
    }

    /**
     * Set rate type.
     *
     * @param string $rateType
     */
    public function setRateType($rateType)
    {
        $this->rateType = $rateType;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * Set base currency code.
     *
     * @param string $baseCurrencyCode
     */
    public function setBaseCurrencyCode($baseCurrencyCode)
    {
        $this->baseCurrencyCode = CurrencyCode::validate($baseCurrencyCode);;
    }

    /**
     * Set created date.
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Set last modification date.
     *
     * @param \DateTime $modifiedAt
     */
    public function setModifiedAt(\DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }
}
