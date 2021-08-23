<?php

namespace Twillie\Expo\Tests;

use PHPUnit\Framework\TestCase;
use Twillie\Expo\Exceptions\FileDoesntExistException;
use Twillie\Expo\Exceptions\InvalidFileException;
use Twillie\Expo\Exceptions\UnsupportedDriverException;
use Twillie\Expo\Expo;

class ExpoTest extends TestCase
{
    /** @test */
    public function expo_instantiates()
    {
        $expo = new Expo();

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
    public function throws_exception_for_invalid_files()
    {
        $this->expectException(FileDoesntExistException::class);
        Expo::driver('file', ['path' => null]);

        $this->expectException(FileDoesntExistException::class);
        Expo::driver('file', ['path' => '']);

        $this->expectException(FileDoesntExistException::class);
        Expo::driver('file', ['path' => 'foo.json']);

        $this->expectException(InvalidFileException::class);
        Expo::driver('file', ['path' => 'foo.txt']);
    }
}
