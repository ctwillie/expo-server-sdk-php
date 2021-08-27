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
            ->setPriority('default')
            ->setSubtitle('Subtitle')
            ->setBadge(0)
            ->setChannelId('default')
            ->setCategoryId('category-id')
            ->setMutableContent(true);

        $this->assertSame(
            [
                "data" => '{"foo":"bar"}',
                "ttl" => 10,
                "priority" => "default",
                "subtitle" => "Subtitle",
                "badge" => 0,
                "channelId" => "default",
                "categoryId" => "category-id",
                "mutableContent" => true
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
    public function throws_exception_when_data_cannot_be_json_encoded()
    {
        $message = new ExpoMessage();

        // encoded in ISO-8859-1
        $data = "\xE1\xE9\xF3\xFA";

        $this->expectExceptionMessage(
            'Data could not be json encoded.'
        );

        $message->setData(
            compact('data')
        );
    }
}
