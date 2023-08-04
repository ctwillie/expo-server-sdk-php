<?php

namespace ExpoSDK\Tests;

use ExpoSDK\ExpoMessage;
use PHPUnit\Framework\TestCase;

class ExpoMessageTest extends TestCase
{
    /** @test */
    public function an_expo_message_can_be_instantiated()
    {
        $message = new ExpoMessage();

        $this->assertInstanceOf(ExpoMessage::class, $message);
    }

    /** @test */
    public function you_can_set_message_attributes()
    {
        $message = new ExpoMessage();

        $message->setData(['foo' => 'bar'])
            ->setTtl(10)
            ->setTo(['ExponentPushToken[valid-token]', 'invalid-token]'])
            ->setExpiration(10)
            ->setPriority('default')
            ->setSubtitle('Subtitle')
            ->setBadge(0)
            ->setChannelId('default')
            ->setCategoryId('category-id')
            ->setMutableContent(true)
            ->setContentAvailable(true);

        $this->assertSame(
            [
                "to" => ['ExponentPushToken[valid-token]'],
                "data" => ['foo' => 'bar'],
                "ttl" => 10,
                "expiration" => 10,
                "priority" => "default",
                "subtitle" => "Subtitle",
                "badge" => 0,
                "channelId" => "default",
                "categoryId" => "category-id",
                "mutableContent" => true,
                "_contentAvailable" => true,
            ],
            $message->toArray()
        );
    }

    /** @test */
    public function throws_exception_providing_unsupported_priority()
    {
        $message = new ExpoMessage();

        $this->expectExceptionMessage(
            'Priority must be default, normal or high.'
        );

        $message->setPriority('foo');
    }

    /** @test */
    public function throws_exception_if_data_is_not_null_object_or_assoc_array()
    {
        $message = new ExpoMessage();
        $data = ['foo'];

        $this->expectExceptionMessage(sprintf(
            'Message data must be either an associative array, object or null. %s given',
            gettype($data)
        ));

        $message->setData($data);
    }

    /** @test */
    public function can_create_message_from_array()
    {
        $message = (new ExpoMessage([
            'title' => 'test title',
            'body' => 'test body',
            'data' => [],
            'to' => ['ExponentPushToken[valid-token]', 'invalid-token]'],
            '_contentAvailable' => true,
        ]))->toArray();
        $expected = [
            'mutableContent' => false,
            'priority' => 'default',
            'title' => 'test title',
            'body' => 'test body',
            'data' => new \stdClass(),
            'to' => ['ExponentPushToken[valid-token]'],
            '_contentAvailable' => true,
        ];

        asort($expected);
        asort($message);

        $this->assertEquals($expected, $message);
    }

    /** @test */
    public function can_set_sound_properties_on_message()
    {
        $message = new ExpoMessage([
            'sound' => 'alert',
        ]);

        $expected = [
            'sound' => 'alert',
            'mutableContent' => false,
            'priority' => 'default',
        ];

        $this->assertEquals($expected, $message->toArray());

        $message->playSound();
        $expected['sound'] = 'default';

        $this->assertEquals($expected, $message->toArray());
    }
}
