<?php

namespace ExpoSDK\Expo\Drivers;

abstract class Driver
{
    /**
     * Builds the driver instance
     *
     * @param array $config
     * @return void
     */
    abstract protected function build(array $config);

    /**
     * Stores subscriptions for a channel
     *
     * @param string $channel
     * @param array $tokens
     * @return void
     */
    abstract public function store(string $channel, array $tokens);

    /**
     * Retrieves a channels subscriptions
     *
     * @param string $channel
     * @return array|null
     */
    abstract public function retrieve(string $channel);

    /**
     * Removes subscriptions from a channels
     *
     * @param string $channel
     * @param array $tokens
     * @return bool
     */
    abstract public function forget(string $channel, array $tokens);
}
