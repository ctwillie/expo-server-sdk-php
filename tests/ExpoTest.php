<?php

namespace ExpoSDK\Tests;

use ExpoSDK\Exceptions\InvalidTokensException;
use ExpoSDK\Exceptions\UnsupportedDriverException;
use ExpoSDK\Expo;
use ExpoSDK\ExpoMessage;
use ExpoSDK\File;
use PHPUnit\Framework\TestCase;

class ExpoTest extends TestCase
{
    private $path = __DIR__ . '/storage/expo.json';
    private $file = null;

    protected function setUp(): void
    {
        $this->file = new File($this->path);
    }

    protected function tearDown(): void
    {
        $this->file->empty();
    }

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

    /**
     * @test
     * @depends expo_instantiates
     */
    public function methods_return_expo_instance(Expo $expo)
    {
        $expo = $expo->send(new ExpoMessage());
        $this->assertInstanceOf(Expo::class, $expo);

        $expo = $expo->setAccessToken('access-token');
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
    public function throws_exception_with_no_valid_tokens(Expo $expo)
    {
        $valid = 'invalid-token[yyy-yyy-yyy]';
        $invalid = 'another-invalid-token[xxx-xxx-xxx]';

        $this->expectExceptionMessage(
            'No valid expo tokens provided.'
        );

        $expo->to([$valid, $invalid]);
    }

    /** @test */
    public function an_expo_message_can_be_built()
    {
        $message = (new ExpoMessage())
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

    /**
     * @test
     * @depends expo_instantiates
     */
    public function to_channel_method_sets_recipients(Expo $expo)
    {
        $token = 'ExponentPushToken[xxx-xxx-xxx]';
        $channel = 'default';
        $expo->subscribe($channel, $token);
        $expo->toChannel($channel);

        $this->assertSame(
            [$token],
            $expo->getRecipients($channel)
        );
    }

    /**
     * @test
     * @depends expo_instantiates
     */
    public function can_determine_if_a_channel_has_subscriptions(Expo $expo)
    {
        $token = 'ExponentPushToken[xxx-xxx-xxx]';
        $channel = 'default';
        $expo->subscribe($channel, $token);

        $hasSubscriptions = $expo->hasSubscriptions($channel);
        $this->assertTrue($hasSubscriptions);

        $expo->unsubscribe($channel, $token);

        $hasSubscriptions = $expo->hasSubscriptions($channel);
        $this->assertFalse($hasSubscriptions);
    }

    /** @test */
    public function throws_exception_checking_subscriptions_without_driver()
    {
        $expo = new Expo();
        $message = 'You must provide a driver to interact with subscriptions.';

        $this->expectExceptionMessage($message);
        $expo->hasSubscriptions('default', []);
    }

    /** @test */
    public function throws_exception_retrieving_subscriptions_without_driver()
    {
        $expo = new Expo();
        $message = 'You must provide a driver to interact with subscriptions.';

        $this->expectExceptionMessage($message);
        $expo->getSubscriptions('default', []);
    }

    /** @test */
    public function throws_exception_subscribing_token_without_driver()
    {
        $expo = new Expo();
        $message = 'You must provide a driver to interact with subscriptions.';

        $this->expectExceptionMessage($message);
        $expo->subscribe('default', []);
    }

    /** @test */
    public function throws_exception_unsubscribing_token_without_driver()
    {
        $expo = new Expo();
        $message = 'You must provide a driver to interact with subscriptions.';

        $this->expectExceptionMessage($message);
        $expo->unsubscribe('default', []);
    }

    /** @test */
    public function throws_exception_when_push_called_with_no_messages()
    {
        $expo = new Expo();

        $this->expectExceptionMessage('You must have at least one message to push');

        $expo->push();
    }

    /** @test */
    public function converts_arrays_to_expo_messages() {
        $messages = [
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
            (new ExpoMessage)->setData(['foo' => 'bar'])
                ->setTtl(10)
                ->setTo(['ExponentPushToken[valid-token]', 'invalid-token]'])
                ->setExpiration(10)
                ->setPriority('default')
                ->setSubtitle('Subtitle')
                ->setBadge(0)
                ->setChannelId('default')
                ->setCategoryId('category-id')
                ->setMutableContent(true),
        ];

        $expoMessages = (new Expo)->send($messages)->getMessages();

        foreach ($expoMessages as $message) {
            if (! $message instanceof ExpoMessage) {
                $this->throwException(new \TypeError('Could not create message from array of data'));
            }
        }

        $messages[1] = $messages[1]->toArray();

        $expectedMessages = array_map(function ($message) {
            return $message->toArray();
        }, $expoMessages);

        $this->assertEquals($expectedMessages, $messages);
    }

    /** @test */
    public function throws_exception_if_any_message_does_not_have_recpients()
    {
        $message1 = (new ExpoMessage)
            ->setTitle('Title')
            ->setBody('Message body')
            ->setData(['foo' => 'bar'])
            ->setChannelId('default');

        $message2 = [
            'title' => 'Title',
            'body' => 'Message body',
            'to' => 'ExponentPushToken[xxxxxx]',
        ];

        $this->expectExceptionMessage('A message must have at least one recipient to send');

        (new Expo())->send([$message1, $message2])->push();
    }

    /** @test */
    public function throws_exception_passing_assoc_array_to_send_method()
    {
        $this->expectExceptionMessage(
            'You can only send an ExpoMessage instance or an array of messages'
        );

        (new Expo())->send(['title' => 'Title']);
    }

    /** @test */
    public function can_register_callback_for_unregistered_tokens()
    {
        $expo = new Expo();

        $expo::addDevicesNotRegisteredHandler(function ($tokens) {
            //
        });

        $this->assertTrue($expo::hasMacro('devicesNotRegistered'));
    }

    /** @test */
    public function can_reset_the_instance_message_and_recipients()
    {
        $token = 'ExpoPushToken[xxxxxx]';
        $message = new ExpoMessage(['title' => 'Title']);
        $expo = (new Expo())->send($message)->to($token);

        $this->assertNotEmpty($expo->getRecipients());
        $this->assertNotEmpty($expo->getMessages());

        $expo->reset();

        $this->assertEmpty($expo->getRecipients());
        $this->assertEmpty($expo->getMessages());
    }
}
