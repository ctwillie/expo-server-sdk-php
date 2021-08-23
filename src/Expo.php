<?php

namespace Twillie\Expo;

class Expo
{
    public static function driver(string $driver = 'file', array $config = [])
    {
        return new self(new DriverManager($driver, $config));
    }
}
