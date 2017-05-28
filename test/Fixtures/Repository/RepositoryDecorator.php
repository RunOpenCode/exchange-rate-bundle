<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\Fixtures\Repository;

use RunOpenCode\ExchangeRate\Contract\RepositoryInterface;
use RunOpenCode\ExchangeRate\Enum\RateType;

/**
 * Class RepositoryDecorator
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Fixture\Repository
 */
class RepositoryDecorator implements RepositoryInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var \SplQueue
     */
    private $queue;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->queue = new \SplQueue();
    }

    /**
     * Put mocked method invocation on execution queue
     *
     * @param string $name Method name.
     * @param mixed $will Invocation result or exception to throw.
     *
     * @return RepositoryDecorator $this Fluent interface.
     */
    public function on($name, $will)
    {
        $this->queue->enqueue(['name' => $name, 'value' => $will]);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __call($name, $arguments)
    {
        if (count($this->queue) > 0 && $name === $this->queue->bottom()['name']) {
            $result = $this->queue->dequeue();

            if ($result['value'] instanceof \Exception) {
                throw $result['value'];
            }

            return $result['value'];
        }

        return call_user_func_array(array($this->repository, $name), $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $rates)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function delete(array $rates)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function has($sourceName, $currencyCode, \DateTime $date = null, $rateType = RateType::MEDIAN)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function get($sourceName, $currencyCode, \DateTime $date = null, $rateType = RateType::MEDIAN)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function latest($sourceName, $currencyCode, $rateType = RateType::MEDIAN)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $criteria = array())
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
