<?php

namespace Twillie\Expo\Tests;

use PHPUnit\Framework\TestCase;
use Twillie\Expo\Exceptions\UnsupportedDriverException;
use Twillie\Expo\Expo;

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
