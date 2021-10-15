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
            ->setMutableContent(true);

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
    public function can_create_message_from_array()
    {
        $message = (new ExpoMessage([
            'title' => 'test title',
            'body' => 'test body',
            'data' => ['test' => 'data'],
            'to' => ['ExponentPushToken[valid-token]', 'invalid-token]'],
        ]))->toArray();
        $expected = [
            'mutableContent' => false,
            'priority' => 'default',
            'title' => 'test title',
            'body' => 'test body',
            'data' => ['test' => 'data'],
            'to' => ['ExponentPushToken[valid-token]'],
        ];

        asort($expected);
        asort($message);

        $this->assertEquals($expected, $message);
    }
}
