<?php

namespace ExpoSDK;

use ExpoSDK\Drivers\FileDriver;
use ExpoSDK\Exceptions\InvalidTokensException;
use ExpoSDK\Exceptions\UnsupportedDriverException;

class DriverManager
{
    /**
     * @var array
     */
    private $supportedDrivers = [
        'file',
    ];

    /**
     * @var string
     */
    private $driverKey;

    /**
     * @var Driver
     */
    private $driver;

    public function __construct(string $driver, array $config = [])
    {
        $this->validateDriver($driver)
            ->buildDriver($config);
    }

    private function validateDriver(string $driver)
    {
        $this->driverKey = strtolower($driver);

        if (! in_array($this->driverKey, $this->supportedDrivers)) {
            throw new UnsupportedDriverException(sprintf(
                'Driver %s is not supported',
                $driver
            ));
        }

        return $this;
    }

    private function buildDriver(array $config)
    {
        if ($this->driverKey === 'file') {
            $this->driver = new FileDriver($config);
        }
    }

    /**
     * Subscribes tokens to a channel
     *
     * @param string $channel
     * @param null|string|array $tokens
     */
    public function subscribe(string $channel, $tokens)
    {
        return $this->driver->store(
            $this->normalizeChannel($channel),
            $this->normalizeTokens($tokens)
        );
    }

    /**
     * Get a channels tokens
     *
     * @param string $channel
     * @return array|null
     */
    public function getSubscriptions(string $channel)
    {
        return $this->driver->retrieve(
            $this->normalizeChannel($channel)
        );
    }

    /**
     * Unsubscribes tokens from a channel
     *
     * @param string $channel
     * @param null|string|array $tokens
     */
    public function unsubscribe(string $channel, $tokens)
    {
        return $this->driver->forget(
            $this->normalizeChannel($channel),
            $this->normalizeTokens($tokens)
        );
    }

    /**
     * Normalizes the channel name
     *
     * @param string $channel
     * @return string
     */
    private function normalizeChannel(string $channel)
    {
        return trim(strtolower($channel));
    }

    /**
     * Normalizes tokens to be an array
     *
     * @param string|array $tokens
     * @return array
     * @throws InvalidTokensException
     */
    private function normalizeTokens($tokens)
    {
        if (is_array($tokens) && count($tokens) > 0) {
            return $tokens;
        }

        if (is_string($tokens)) {
            return [$tokens];
        }

        throw new InvalidTokensException(
            'Tokens must be a string or non empty array'
        );
    }
}
