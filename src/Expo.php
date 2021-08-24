<?php

namespace Twillie\Expo;

use Twillie\Expo\Exceptions\BadMethodCallException;

class Expo
{
    /**
     * @var DriverManager
     */
    private $manager;

    public function __construct(DriverManager $manager = null)
    {
        $this->manager = $manager ?? new DriverManager('file', []);
    }

    public static function driver(string $driver = 'file', array $config = [])
    {
        return new self(new DriverManager($driver, $config));
    }

    /**
     * Subscribes tokens to a channel
     *
     * @param string $channel
     * @param string|array $tokens
     */
    public function subscribe(string $channel, $tokens = null)
    {
        return $this->manager->subscribe($channel, $tokens);
    }

    /**
     * Retrieve a channels subscriptions
     *
     * @param string $channel
     * @return array|null
     */
    public function getSubscriptions(string $channel)
    {
        return $this->manager->getSubscriptions($channel);
    }

    /**
     * Unsubscribes tokens from a channel
     *
     * @param string $channel
     * @param string|array $tokens
    */
    public function unsubscribe(string $channel, $tokens = null)
    {
        return $this->manager->unsubscribe($channel, $tokens);
    }
}
