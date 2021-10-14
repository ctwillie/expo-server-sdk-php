<?php

namespace ExpoSDK;

use ExpoSDK\Exceptions\ExpoException;
use ExpoSDK\Exceptions\InvalidTokensException;
use ExpoSDK\Traits\Macroable;

class Expo
{
    use Macroable;

    /**
     * @var DriverManager
     */
    private $manager;

    /**
     * @var ExpoClient
     */
    private $client;

    /**
     * Messages to send
     *
     * @var ExpoMessage[]
     */
    private $messages = [];

    /**
     * Default tokens to send the message to (if they don't have their own respective recipients)
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
     * Registers macro for handling each token with DeviceNotRegistered error separately
     * @param $callback
     */
    public static function addDeviceNotRegisteredHandler($callback) {
        self::macro('deviceNotRegistered', $callback);
    }

    /**
     * Registers macro for handling all tokens with DeviceNotRegistered errors
     * @param $callback
     */
    public static function addDevicesNotRegisteredHandler($callback) {
        self::macro('devicesNotRegistered', $callback);
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
     * Get default recipients
     *
     * @return array|null
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Get messages
     *
     * @return array|null
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Sets the messages to send
     *
     * @param ExpoMessage[]|ExpoMessage|array $message
     */
    public function send($message): self
    {
        $messages = Utils::arrayWrap($message);

        foreach ($messages as $index => $message) {
            if (!($message instanceof ExpoMessage))
                $messages[$index] = new ExpoMessage($message);
        }

        $this->messages = $messages;

        return $this;
    }

    /**
     * Sets default recipients
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
     * Send the messages to the expo server
     *
     * @throws ExpoException
     */
    public function push(): ExpoResponse
    {
        if (empty($this->messages)) {
            throw new ExpoException('You must have messages to push');
        }

        $messages = [];

        /**
         * When response ticket has DeviceNotRegistered it has no indication which push token produced this error,
         * however it is known that the order of messages and response tickets are the same.
         * So the only way to keep track of invalid tokens is by their indexes.
         * For this to work we need to flatten messages' recipients, e.g.
         * {
         *     'to' => ['token1', 'token2'],
         *     'body' => 'Message body',
         * }
         * would turn into
         * [
         *     {
         *         'to' => 'token1',
         *         'body' => 'Message body',
         *     },
         *     {
         *         'to' => 'token2',
         *         'body' => 'Message body',
         *     },
         * ]
         */
        foreach ($this->messages as $message) {
            $message = $message->toArray();
            foreach (Utils::arrayWrap($message['to'] ?? $this->recipients) as $token) {
                $messages[] = array_merge($message, ['to' => $token]);
            }
        }

        $this->reset();

        // todo chunking when messages count > 100, accumulate client responses somehow
        // $responses = [];
        // $chunks = array_chunk($messages, 100);
        // foreach ($chunks as $chunk) {
        //     $responses[] = $this->client->sendPushNotifications($chunk);
        // }
        // return $responses;

        $response = $this->client->sendPushNotifications($messages);

        if (self::hasMacro('deviceNotRegistered') || self::hasMacro('devicesNotRegistered')) {
            $notRegisteredTokens = [];

            foreach ($response->getData() as $index => $ticket) {
                if (($ticket['details']['error'] ?? '') == 'DeviceNotRegistered')
                    $notRegisteredTokens[] = $messages[$index]['to'];
            }

            if (self::hasMacro('devicesNotRegistered')) {
                $this->devicesNotRegistered($notRegisteredTokens);
            } else if (self::hasMacro('deviceNotRegistered')) {
                foreach ($notRegisteredTokens as $token) {
                    $this->deviceNotRegistered($token);
                }
            }
        }

        return $response;
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
        $this->messages = [];
        $this->recipients = null;
    }
}
