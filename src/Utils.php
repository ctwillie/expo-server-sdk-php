<?php

namespace ExpoSDK;

class Utils
{
    /**
     * Check if a value is a valid Expo push token
     *
     * @param mixed $value
     * @return bool
     */
    public static function isExpoPushToken($value)
    {
        if (! is_string($value) || strlen($value) < 15) {
            return false;
        }

        return (self::strStartsWith($value, 'ExponentPushToken[') ||
            self::strStartsWith($value, 'ExpoPushToken[')) &&
            self::strEndsWith($value, ']');
    }

    /**
     * Check if a string starts with another
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function strStartsWith(string $haystack, string $needle)
    {
        return (string)$needle !== '' &&
            strncmp($haystack, $needle, strlen($needle)) === 0;
    }

    /**
     * Check if a string ends with another
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function strEndsWith(string $haystack, string $needle)
    {
        return $needle !== '' &&
            substr($haystack, -strlen($needle)) === (string) $needle;
    }
}
