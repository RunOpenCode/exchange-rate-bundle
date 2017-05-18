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

/**
 * Class RepositoryDecorator
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Fixture\Repository
 */
class RepositoryDecorator
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

    public function on($name, $will)
    {
        $this->queue->enqueue(['name' => $name, 'value' => $will]);
        return $this;
    }

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
}
