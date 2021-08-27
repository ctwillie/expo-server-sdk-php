<?php

namespace ExpoSDK\Tests;

use ExpoSDK\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    /** @test */
    public function can_identify_valid_expo_tokens()
    {
        $this->assertFalse(
            Utils::isExpoPushToken('foo')
        );

        $this->assertFalse(
            Utils::isExpoPushToken('ExpoPushToken[')
        );

        $this->assertTrue(
            Utils::isExpoPushToken('ExpoPushToken[aaaabbbbccccdddd]')
        );

        $this->assertTrue(
            Utils::isExpoPushToken('ExponentPushToken[aaaabbbbccccdddd]')
        );
    }

}
