<?php

namespace ExpoSDK;

use ExpoSDK\Exceptions\ExpoException;
use ExpoSDK\Exceptions\InvalidTokensException;

class Expo
{
    /**
     * @var DriverManager
     */
    private $manager;

    /**
     * @var ExpoClient
     */
    private $client;

    /**
     * The message to send
     *
     * @var ExpoMessage
     */
    private $message = null;

    /**
     * The tokens to send the message to
     *
     * @var array
     */
    private $recipients = null;

    public function __construct(DriverManager $manager = null)
    {
        $this->manager = $manager;

        $this->client = new ExpoClient();
    }

    /**
     * Builds an expo instance
     */
    public static function driver(string $driver = 'file', array $config = []): self
    {
        $manager = new DriverManager($driver, $config);

        return new self($manager);
    }

    /**
     * Subscribes tokens to a channel
     *
     * @param string|array $tokens
     * @return mixed
     * @throws ExpoException
     */
    public function subscribe(string $channel, $tokens = null)
    {
        if ($this->manager) {
            return $this->manager->subscribe($channel, $tokens);
        }

        throw new ExpoException('You must provide a driver to interact with subscriptions.');
    }

    /**
     * Unsubscribes tokens from a channel
     *
     * @param string|array $tokens
     * @return mixed
     * @throws ExpoException
    */
    public function unsubscribe(string $channel, $tokens = null)
    {
        if ($this->manager) {
            return $this->manager->unsubscribe($channel, $tokens);
        }

        throw new ExpoException('You must provide a driver to interact with subscriptions.');
    }

    /**
     * Set the recipients from channel subscriptions to send the message to
     */
    public function toChannel(string $channel): self
    {
        $this->recipients = $this->getSubscriptions($channel);

        return $this;
    }

    /**
     * Retrieves a channels subscriptions
     *
     * @return array|null
     * @throws ExpoException
     */
    public function getSubscriptions(string $channel)
    {
        if ($this->manager) {
            return $this->manager->getSubscriptions($channel);
        }

        throw new ExpoException('You must provide a driver to interact with subscriptions.');
    }

    /**
     * Checks if a channel has subscriptions
     *
     * @throws ExpoException
     */
    public function hasSubscriptions(string $channel): bool
    {
        if ($this->manager) {
            return (bool) $this->manager->getSubscriptions($channel);
        }

        throw new ExpoException(
            'You must provide a driver to interact with subscriptions.'
        );
    }

    /**
     * Check if a value is a valid Expo push token
     *
     * @param mixed $value
     */
    public function isExpoPushToken($value): bool
    {
        return Utils::isExpoPushToken($value);
    }

    /**
     * Get the message recipients
     *
     * @return array|null
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Sets the message to send
     */
    public function send(ExpoMessage $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Sets the recipients to send the message to
     *
     * @param string|array $recipients
     * @throws InvalidTokensException
     * @throws ExpoException
     */
    public function to($recipients = null): self
    {
        $this->recipients = Utils::validateTokens($recipients);

        return $this;
    }

    /**
     * Send the message to the expo server
     *
     * @throws ExpoException
     */
    public function push(): ExpoResponse
    {
        if (is_null($this->message) || is_null($this->recipients)) {
            throw new ExpoException('You must have a message and recipients to push');
        }

        $messages = array_map(function ($recipient) {
            return $this->message->toArray() + ['to' => $recipient];
        }, $this->recipients);

        $this->reset();

        return $this->client->sendPushNotifications($messages);
    }

    /**
     * Fetches the push notification receipts from the expo server
     *
     * @throws ExpoException
     */
    public function getReceipts(array $ticketIds): ExpoResponse
    {
        $ticketIds = array_filter($ticketIds, function ($id) {
            return is_string($id);
        });

        return $this->client->getPushNotificationReceipts($ticketIds);
    }

    /**
     * Set the Expo access token
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->client->setAccessToken($accessToken);

        return $this;
    }

    /**
     * Resets the instance data
     */
    private function reset(): void
    {
        $this->message = null;
        $this->recipients = null;
    }
}
