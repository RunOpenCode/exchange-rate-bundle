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
     * @var \SplStack
     */
    private $callStack;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->callStack = new \SplStack();
    }

    public function on($name, $will)
    {
        $this->callStack->push(['name' => $name, 'value' => $will]);
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (count($this->callStack) > 0 && $name === $this->callStack->top()['name']) {
            $result = $this->callStack->pop();

            if ($result['value'] instanceof \Exception) {
                throw $result['value'];
            }

            return $result['value'];
        }

        return call_user_func_array(array($this->repository, $name), $arguments);
    }
}
