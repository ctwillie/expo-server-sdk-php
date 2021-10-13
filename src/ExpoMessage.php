<?php

namespace ExpoSDK;

use ExpoSDK\Exceptions\ExpoMessageException;

class ExpoMessage
{
    /** @var null */
    private $data = null;

    /** @var null */
    private $title = null;

    /** @var null */
    private $body = null;

    /** @var null */
    private $ttl = null;

    /** @var string */
    private $priority = 'default';

    /** @var null */
    private $subtitle = null;

    /** @var null */
    private $sound = null;

    /** @var null */
    private $badge = null;

    /** @var null */
    private $channelId = null;

    /** @var null */
    private $categoryId = null;

    /** @var bool */
    private $mutableContent = false;

    /**
     * Sets the data for the message
     *
     * @param object|array|null $data
     *
     * @throws ExpoMessageException
     */
    public function setData($data = null): self
    {
        // allow null, objects and associative arrays
        if ($data != null && !is_object($data) && !Utils::isAssoc($data)) {
            throw new ExpoMessageException(
                'Message data must be either associative array, object or null.'
            );
        }

        $this->data = $data;

        return $this;
    }

    /**
     * Sets the title to display in the notification
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Sets the message to display in the notification
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Sets the number of seconds for which the message may be kept around for redelivery
     */
    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Sets the delivery priority of the message, either 'default', 'normal' or 'high
     *
     * @throws ExpoMessageException
     */
    public function setPriority(string $priority): self
    {
        $priority = strtolower($priority);

        if (! in_array($priority, ['default', 'normal', 'high'])) {
            throw new ExpoMessageException(
                'Priority must be default, normal or high.'
            );
        }

        $this->priority = $priority;

        return $this;
    }

    /**
     * Sets the subtitle to display in the notification below the title
     */
    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Play a sound when the recipient receives the notification
     */
    public function playSound(): self
    {
        $this->sound = 'default';

        return $this;
    }

    /**
     * Set the number to display in the badge on the app icon
     */
    public function setBadge(int $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Set the ID of the Notification Channel through which to display this notification
     */
    public function setChannelId(string $channelId): self
    {
        $this->channelId = $channelId;

        return $this;
    }

    /**
     * Set the ID of the notification category that this notification is associated with
     */
    public function setCategoryId(string $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Set whether the notification can be intercepted by the client app
     */
    public function setMutableContent(bool $mutable): self
    {
        $this->mutableContent = $mutable;

        return $this;
    }

    /**
     * Convert the message to an array
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
