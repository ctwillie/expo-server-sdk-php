<?php

namespace ExpoSDK;

use ExpoSDK\Exceptions\ExpoException;
use ExpoSDK\Exceptions\InvalidTokensException;

class Utils
{
    /**
     * Check if a value is a valid Expo push token
     *
     * @param mixed $value
     */
    public static function isExpoPushToken($value): bool
    {
        if (! is_string($value) || strlen($value) < 15) {
            return false;
        }

        return (self::strStartsWith($value, 'ExponentPushToken[') ||
            self::strStartsWith($value, 'ExpoPushToken[')) &&
            self::strEndsWith($value, ']');
    }

    /**
     * Determine if an array is an asociative array
     *
     * The check determines if the array has sequential numeric
     * keys. If it does not, it is considered an associative array.
     */
    public static function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Check if a string starts with another
     */
    public static function strStartsWith(string $haystack, string $needle): bool
    {
        return (string)$needle !== '' &&
            strncmp($haystack, $needle, strlen($needle)) === 0;
    }

    /**
     * Check if a string ends with another
     */
    public static function strEndsWith(string $haystack, string $needle): bool
    {
        return $needle !== '' &&
            substr($haystack, -strlen($needle)) === (string) $needle;
    }

    /**
     * Wrap data in array if data is not an array
     *
     * @param mixed $data
     *
     * @return array
     */
    public static function arrayWrap($data): array
    {
        return is_array($data) ? $data : [$data];
    }

    /**
     * Validates and filters tokens for later use
     *
     * @throws \ExpoSDK\Exceptions\ExpoException
     * @throws \ExpoSDK\Exceptions\InvalidTokensException
     *
     * @param string[]|string $tokens
     *
     * @return string[]
     */
    public static function validateTokens($tokens): array
    {
        if (! is_array($tokens) && ! is_string($tokens)) {
            throw new InvalidTokensException(sprintf(
                'Tokens must be a string or non empty array, %s given.',
                gettype($tokens)
            ));
        }

        $tokens = array_filter(Utils::arrayWrap($tokens), function ($token) {
            return Utils::isExpoPushToken($token);
        });

        if (count($tokens) === 0) {
            throw new ExpoException('No valid expo tokens provided.');
        }

        return $tokens;
    }
}
