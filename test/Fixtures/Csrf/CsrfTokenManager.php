<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\Fixtures\Csrf;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class CsrfTokenManager
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Tests\Fixtures\Source
 */
class CsrfTokenManager implements CsrfTokenManagerInterface
{
    /**
     * @var \SplQueue
     */
    private $queue;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $manager;

    public function __construct(CsrfTokenManagerInterface $manager)
    {
        $this->queue = new \SplQueue();
        $this->manager = $manager;
    }

    /**
     * Put mocked method invocation on execution queue
     *
     * @param string $name Method name.
     * @param mixed $will Invocation result or exception to throw.
     *
     * @return CsrfTokenManager $this Fluent interface.
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

        return call_user_func_array(array($this->manager, $name), $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function getToken($tokenId)
    {
        return $this->__call(__FUNCTION__, [$tokenId]);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshToken($tokenId)
    {
        return $this->__call(__FUNCTION__, [$tokenId]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken($tokenId)
    {
        return $this->__call(__FUNCTION__, [$tokenId]);
    }

    /**
     * {@inheritdoc}
     */
    public function isTokenValid(CsrfToken $token)
    {
        return $this->__call(__FUNCTION__, [$token]);
    }
}
