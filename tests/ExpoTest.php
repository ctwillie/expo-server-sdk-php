<?php

namespace ExpoSDK\Expo\Tests;

use PHPUnit\Framework\TestCase;
use ExpoSDK\Expo\Exceptions\UnsupportedDriverException;
use ExpoSDK\Expo\Expo;

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
}
