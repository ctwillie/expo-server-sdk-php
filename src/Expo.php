<?php

namespace ExpoSDK\Expo;

class Expo
{
    /**
     * @var DriverManager
     */
    private $manager;

    /**
     * Expo constructor
     *
     * @param DriverManager $manager
     */
    public function __construct(DriverManager $manager = null)
    {
        $this->manager = $manager ?? new DriverManager('file', []);
    }

    /**
     * Builds an expo instance
     *
     * @param string $driver
     * @param array $config
     * @return Expo
     */
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
     * Retrieves a channels subscriptions
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

    /**
     * Check if a value is a valid Expo push token
     *
     * @param mixed $value
     * @return bool
     */
    public function isExpoPushToken($value)
    {
        if (! is_string($value) || strlen($value) < 15) {
            return false;
        }

        return Helper::isExpoPushToken($value);
    }
}
