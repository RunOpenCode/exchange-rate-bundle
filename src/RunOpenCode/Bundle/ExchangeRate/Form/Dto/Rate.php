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
use Symfony\Component\Validator\Constraints as Assert;
use RunOpenCode\Bundle\ExchangeRate\Validator\Constraints as ExchangeRateAssert;

/**
 * Class Rate
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Form\Dto
 *
 * @ExchangeRateAssert\ExchangeRate()
 */
class Rate implements RateInterface
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $rate;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="float")
     */
    private $value;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ExchangeRateAssert\BaseCurrency()
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
        $this->baseCurrencyCode = $baseCurrencyCode;
        $this->setValue($value);
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
        $this->value = (null !== $value) ? (float) $value : null;
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
     * {@inheritdoc}
     */
    public function getSourceName()
    {
        if (false !== strpos($this->getRate(), '.')) {
            return explode('.', $this->getRate())[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode()
    {
        if (false !== strpos($this->getRate(), '.')) {
            return explode('.', $this->getRate())[2];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRateType()
    {
        if (false !== strpos($this->getRate(), '.')) {
            return explode('.', $this->getRate())[1];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return null; // unknown
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedAt()
    {
        return null; // unknown
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
