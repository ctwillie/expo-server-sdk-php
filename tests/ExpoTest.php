<?php

namespace ExpoSDK\Expo\Tests;

use ExpoSDK\Expo\Exceptions\InvalidTokensException;
use ExpoSDK\Expo\Exceptions\UnsupportedDriverException;
use ExpoSDK\Expo\Expo;
use ExpoSDK\Expo\ExpoMessage;
use PHPUnit\Framework\TestCase;

class ExpoTest extends TestCase
{
    private $path = __DIR__ . '/storage/expo.json';

    /** @test */
    public function expo_instantiates()
    {
        $expo = Expo::driver('file', [
            'path' => $this->path,
        ]);

        $this->assertInstanceOf(Expo::class, $expo);

        return $expo;
    }

    /** @test */
    public function only_accepts_supported_drivers()
    {
        $this->expectException(UnsupportedDriverException::class);

        Expo::driver('foo');
    }

    /** @test */
    public function throws_exception_trying_to_subscribe_without_driver()
    {
        $expo = new Expo();

        $this->expectExceptionMessage('You must provide a driver to interact with subscriptions.');

        $expo->subscribe('default', []);
    }

    /**
     * @test
     * @depends expo_instantiates
     */
    public function can_identify_valid_expo_tokens(Expo $expo)
    {
        $result = $expo->isExpoPushToken('foo');
        $this->assertFalse($result);

        $result = $expo->isExpoPushToken('ExpoPushToken[');
        $this->assertFalse($result);

        $result = $expo->isExpoPushToken('ExponentPushToken[');
        $this->assertFalse($result);

        $result = $expo->isExpoPushToken('ExpoPushToken[aaaabbbbccccdddd]');
        $this->assertTrue($result);

        $result = $expo->isExpoPushToken('ExponentPushToken[aaaabbbbccccdddd]');
        $this->assertTrue($result);
    }

    /**
     * @test
     * @depends expo_instantiates
     */
    public function send_method_returns_expo(Expo $expo)
    {
        $expo = $expo->send(new ExpoMessage());

        $this->assertInstanceOf(Expo::class, $expo);
    }

    /**
     * @test
     * @depends expo_instantiates
     */
    public function can_set_message_recipients(Expo $expo)
    {
        $token = 'ExponentPushToken[xxx-xxx-xxx]';
        $expo->to($token);
        $this->assertSame([$token], $expo->getRecipients());

        $tokens = ['ExponentPushToken[xxx-xxx-xxx]', 'ExponentPushToken[yyy-yyy-yyy]'];
        $expo->to($tokens);
        $this->assertSame($tokens, $expo->getRecipients());
    }

    /**
     * @test
     * @depends expo_instantiates
     */
    public function expo_throws_exception_for_invalid_recipients(Expo $expo)
    {
        $tokens = null;
        $this->expectException(InvalidTokensException::class);
        $expo->to($tokens);

        $tokens = ['invalid-token', 'another-fake-token'];
        $this->expectExceptionMessage('No valid expo tokens supplied.');
        $expo->to($tokens);
    }

    /**
     * @test
     * @depends expo_instantiates
     */
    public function expo_filters_non_valid_tokens(Expo $expo)
    {
        $valid = 'ExponentPushToken[yyy-yyy-yyy]';
        $invalid = 'InvalidToken[xxx-xxx-xxx]';
        $expo->to([$valid, $invalid]);

        $this->assertSame(
            [$valid],
            $expo->getRecipients()
        );
    }

    /** @test */
    public function an_expo_message_can_be_built()
    {
        $message = (new ExpoMessage())
            ->setTitle('Test Title')
            ->setTitle('Test title')
            ->setBody('Test message body')
            ->setPriority('default')
            ->playSound();

        $expected = [
            "title" => "Test title",
            "body" => "Test message body",
            "priority" => "default",
            "sound" => "default",
            "mutableContent" => false,
        ];

        $this->assertSame($expected, $message->toArray());
    }

    /** @test */
    public function throws_exception_when_push_called_with_empty_message_or_recipients()
    {
        $expo = new Expo();

        $this->expectExceptionMessage('You must have a message and recipients to push');

        $expo->push();
    }

    /**
     * @test
     * @depends expo_instantiates
     */
    public function test_true(Expo $expo)
    {
        $this->assertTrue(true);
    }
}
