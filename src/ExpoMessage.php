<?php

namespace ExpoSDK;

use ExpoSDK\Exceptions\ExpoMessageException;

/**
 * Implementation of Expo message request format
 *
 * @link https://docs.expo.dev/push-notifications/sending-notifications/#message-request-format Expo Message request format
 */
class ExpoMessage
{
    /**
     * An Expo push token or an array of Expo push tokens specifying the recipient(s) of this message.
     *
     * @var string[]|null
     */
    private $to = null;

    /**
     * A JSON object delivered to your app.
     * It may be up to about 4KiB; the total notification payload sent to Apple and Google must be at most 4KiB
     * or else you will get a "Message Too Big" error.
     *
     * @var object|array|null
     */
    private $data = null;

    /**
     * The title to display in the notification.
     * Often displayed above the notification body.
     *
     * @var string|null
     */
    private $title = null;

    /**
     * The message to display in the notification.
     *
     * @var string|null
     */
    private $body = null;

    /**
     * Time to Live: the number of seconds for which the message may be kept around for redelivery if it hasn't been delivered yet.
     * Defaults to null in order to use the respective defaults of each provider (0 for iOS/APNs and 2419200 (4 weeks) for Android/FCM).
     *
     * @var int|null
     */
    private $ttl = null;

    /**
     * Timestamp since the UNIX epoch specifying when the message expires.
     * Same effect as ttl (ttl takes precedence over expiration).
     *
     * @var int|null
     */
    private $expiration = null;

    /**
     * The delivery priority of the message.
     * Specify "default" or omit this field to use the default priority on each platform ("normal" on Android and "high" on iOS).
     *
     * Supported: 'default', 'normal', 'high'.
     *
     * @var string
     */
    private $priority = 'default';

    /**
     * The subtitle to display in the notification below the title.
     *
     * iOS only.
     *
     * @var string|null
     */
    private $subtitle = null;

    /**
     * Play a sound when the recipient receives this notification.
     * Specify "default" to play the device's default notification sound, or omit this field to play no sound.
     * Custom sounds are not supported.
     *
     * iOS only.
     *
     * @var string|null
     */
    private $sound = null;

    /**
     * Number to display in the badge on the app icon.
     * Specify zero to clear the badge.
     *
     * iOS only.
     *
     * @var numeric|null
     */
    private $badge = null;

    /**
     * ID of the Notification Channel through which to display this notification.
     * If an ID is specified but the corresponding channel does not exist on the device (i.e. has not yet been created by your app),
     * the notification will not be displayed to the user.
     *
     * Android only.
     *
     * @var string|null
     */
    private $channelId = null;

    /**
     * ID of the notification category that this notification is associated with.
     * Must be on at least SDK 41 or bare workflow.
     *
     * @see https://docs.expo.dev/versions/latest/sdk/notifications/#managing-notification-categories-interactive-notifications Notification categories
     *
     * @var string|null
     */
    private $categoryId = null;

    /**
     * Specifies whether this notification can be intercepted by the client app.
     * In Expo Go, this defaults to true, and if you change that to false, you may experience issues.
     * In standalone and bare apps, this defaults to false.
     *
     * iOS only.
     *
     * @var bool
     */
    private $mutableContent = false;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * Set recipients of the message
     *
     * @see to
     *
     * @throws \ExpoSDK\Exceptions\ExpoException
     * @throws \ExpoSDK\Exceptions\InvalidTokensException
     *
     * @param  string[]|string  $tokens
     *
     * @return $this
     */
    public function setTo($tokens): self
    {
        $this->to = Utils::validateTokens($tokens);

        return $this;
    }

    /**
     * Sets the data for the message
     *
     * @see data
     *
     * @throws ExpoMessageException
     *
     * @param  mixed  $data
     *
     * @return $this
     */
    public function setData($data = null): self
    {
        if (gettype($data) === 'array' && empty($data)) {
            $data = new \stdClass();
        }

        if ($data !== null && ! is_object($data) && ! Utils::isAssoc($data)) {
            throw new ExpoMessageException(sprintf(
                'Message data must be either an associative array, object or null. %s given',
                gettype($data)
            ));
        }

        $this->data = $data;

        return $this;
    }

    /**
     * Sets the title to display in the notification
     *
     * @see title
     *
     * @param  string|null  $title
     *
     * @return $this
     */
    public function setTitle(string $title = null): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Sets the message to display in the notification
     *
     * @see body
     *
     * @param  string|null  $body
     *
     * @return $this
     */
    public function setBody(string $body = null): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Sets the number of seconds for which the message may be kept around for redelivery
     *
     * @see ttl
     *
     * @param  int|null  $ttl
     *
     * @return $this
     */
    public function setTtl(int $ttl = null): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Sets expiration time of the message
     *
     * @see expiration
     *
     * @param  int|null  $expiration
     *
     * @return $this
     */
    public function setExpiration(int $expiration = null): self
    {
        $this->expiration = $expiration;

        return $this;
    }

    /**
     * Sets the delivery priority of the message, either 'default', 'normal' or 'high
     *
     * @see priority
     *
     * @throws ExpoMessageException
     *
     * @param  string  $priority
     *
     * @return $this
     */
    public function setPriority(string $priority = 'default'): self
    {
        $priority = strtolower($priority);

        if (! in_array($priority, ['default', 'normal', 'high'])) {
            throw new ExpoMessageException('Priority must be default, normal or high.');
        }

        $this->priority = $priority;

        return $this;
    }

    /**
     * Sets the subtitle to display in the notification below the title
     *
     * @see subtitle
     *
     * @param  string|null  $subtitle
     *
     * @return $this
     */
    public function setSubtitle(string $subtitle = null): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Play a sound when the recipient receives the notification
     *
     * @see sound
     *
     * @return $this
     */
    public function playSound(): self
    {
        $this->sound = 'default';

        return $this;
    }

    /**
     * Sets the sound to play when the notification is recieved
     *
     * @see sound
     * @see playSound()
     *
     * @param  string|null  $sound
     *
     * @return $this
     */
    public function setSound(string $sound = null): self
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * Set the number to display in the badge on the app icon
     *
     * @see badge
     *
     * @param  int|null  $badge
     *
     * @return $this
     */
    public function setBadge(int $badge = null): self
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Set the ID of the Notification Channel through which to display this notification
     *
     * @see channelId
     *
     * @param  string|null  $channelId
     *
     * @return $this
     */
    public function setChannelId(string $channelId = null): self
    {
        $this->channelId = $channelId;

        return $this;
    }

    /**
     * Set the ID of the notification category that this notification is associated with
     *
     * @see categoryId
     *
     * @param  string|null  $categoryId
     *
     * @return $this
     */
    public function setCategoryId(string $categoryId = null): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Set whether the notification can be intercepted by the client app
     *
     * @see mutableContent
     *
     * @param  bool  $mutable
     *
     * @return $this
     */
    public function setMutableContent(bool $mutable): self
    {
        $this->mutableContent = $mutable;

        return $this;
    }

    /**
     * Convert the message to an array
     * Skips properties with 'null' values
     *
     * @return array
     */
    public function toArray(): array
    {
        $attributes = [];

        foreach ($this as $key => $value) {
            if (! is_null($value)) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }
}
