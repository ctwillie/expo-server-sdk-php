<?php

namespace Twillie\Expo\Tests;

use PHPUnit\Framework\TestCase;
use Twillie\Expo\Exceptions\FileDoesntExistException;
use Twillie\Expo\Exceptions\InvalidFileException;
use Twillie\Expo\Exceptions\InvalidTokensException;
use Twillie\Expo\Expo;
use Twillie\Expo\File;

class FileDriverTest extends TestCase
{
    private $path = __DIR__ . '/storage/expo.json';
    private $file;
    private $expo;

    protected function setUp(): void
    {
        $this->file = new File($this->path);
        $this->file->empty();
        $this->expo = Expo::driver(
            'file', ['path' => $this->path]
        );
    }

    protected function tearDown(): void
    {
        $this->file->empty();
    }

    /** @test */
    public function file_driver_instantiates()
    {
        $expo = Expo::driver(
            'file', ['path' => $this->path]
        );

        $this->assertInstanceOf(Expo::class, $expo);
    }

    /** @test */
    public function throws_exception_for_invalid_files()
    {
        $this->expectException(FileDoesntExistException::class);
        Expo::driver('file', ['path' => null]);

        $this->expectException(FileDoesntExistException::class);
        Expo::driver('file', ['path' => '']);

        $this->expectException(InvalidFileException::class);
        Expo::driver('file', ['path' => 'foo.txt']);

        $this->expectException(FileDoesntExistException::class);
        Expo::driver('file', ['path' => 'foo.json']);
    }

    /** @test */
    public function throws_exception_for_invalid_tokens()
    {
        $channel = 'default';

        $this->expectException(InvalidTokensException::class);
        $tokens = null;
        $this->expo->subscribe($channel, $tokens);

        $this->expectException(InvalidTokensException::class);
        $tokens = new \stdClass();
        $this->expo->subscribe($channel, $tokens);

        $this->expectException(InvalidTokensException::class);
        $tokens = 0;
        $this->expo->subscribe($channel, $tokens);
    }

    /** @test */
    public function can_subscribe_a_single_token_to_a_channel()
    {
        $channel = 'promo';
        $token = 'ExpoPushToken[random-token]';

        $this->expo->subscribe($channel, $token);
        $subscriptions = $this->expo->getSubscriptions($channel);

        $this->assertSame([$token], $subscriptions);
    }

    /** @test */
    public function can_subscribe_multiple_tokens_to_a_channel()
    {
        $channel = 'promo';
        $token1 = 'ExpoPushToken[random-token-1]';
        $token2 = 'ExpoPushToken[random-token-2]';

        $this->expo->subscribe($channel, [$token1, $token2]);
        $subscriptions = $this->expo->getSubscriptions($channel);

        $this->assertSame([$token1, $token2], $subscriptions);
    }

    /** @test */
    public function can_unsubscribe_a_single_token_from_a_channel()
    {
        $channel = 'promo';
        $token1 = 'ExpoPushToken[random-token-1]';
        $token2 = 'ExpoPushToken[random-token-2]';

        $this->expo->subscribe($channel, [$token1, $token2]);
        $subs = $this->expo->getSubscriptions($channel);

        $this->assertSame([$token1, $token2], $subs);

        $this->expo->unsubscribe($channel, $token2);
        $subs = $this->expo->getSubscriptions($channel);

        $this->assertSame([$token1], $subs);
    }

    /** @test */
    public function can_unsubscribe_multiple_tokens_from_a_channel()
    {
        $channel = 'promo';
        $token1 = 'ExpoPushToken[random-token-1]';
        $token2 = 'ExpoPushToken[random-token-2]';
        $token3 = 'ExpoPushToken[random-token-3]';

        $this->expo->subscribe($channel, [$token1, $token2, $token3]);
        $subs = $this->expo->getSubscriptions($channel);

        $this->assertSame([$token1, $token2, $token3], $subs);

        $this->expo->unsubscribe($channel, [$token1, $token2]);
        $subs = $this->expo->getSubscriptions($channel);

        $this->assertSame([$token3], $subs);
    }

    /** @test */
    public function channel_is_deleted_when_all_subscriptions_are_removed()
    {
        $channel = 'promo';
        $token1 = 'ExpoPushToken[random-token-1]';
        $token2 = 'ExpoPushToken[random-token-2]';

        $this->expo->subscribe($channel, [$token1, $token2]);
        $subs = $this->expo->getSubscriptions($channel);

        $this->assertSame([$token1, $token2], $subs);

        $this->expo->unsubscribe($channel, [$token1, $token2]);
        $subs = $this->expo->getSubscriptions($channel);

        $this->assertNull($subs);
    }
}
