<?php

namespace ExpoSDK\Drivers;

use ExpoSDK\Database;

class DatabaseDriver extends Driver
{
    /**
     * The table name for the driver
     */
    private $tableName = 'expo_tokens';

    /**
     * The storage database object
     *
     * @var Database
     */
    private $database;

    public function __construct(array $config)
    {
        $this->build($config);
    }

    /**
     * Builds the driver instance
     */
    protected function build(array $config): void
    {
        $tableName = $config['tableName'] ?? $this->tableName;

        $this->database = new Database($tableName);
    }

    /**
     * Stores subscriptions for a channel
     */
    public function store(string $channel, array $tokens): bool
    {
        $arrayToSave = array_map(function($token) use($channel) {
            return [
                'channel' => $channel,
                'token' => $token,
                'subscribed' => true
            ];
        }, $tokens);

        return $this->class->storeInTable($arrayToSave);
    }

    /**
     * Retrieves a channels subscriptions
     *
     * @return array|null
     */
    public function retrieve(string $channel)
    {
        return $this->class->getFromTable($channel);
    }

    /**
     * Removes subscriptions from a channels
     */
    public function forget(string $channel, array $tokens): bool
    {
        $arrayToSave = array_map(function($token) use($channel) {
            return [
                'channel' => $channel,
                'token' => $token,
                'subscribed' => false
            ];
        }, $tokens);

        return $this->class->deleteFromTable($arrayToSave);
    }
}
