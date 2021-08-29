<?php

namespace ExpoSDK;

use GuzzleHttp\Client;

class ExpoClient
{
    public const EXPO_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * The Expo access token
     *
     * @var string
     */
    private $accessToken = null;

    /**
     * @var GuzzleHttpClient
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Sends push notification messages to the Expo api
     *
     * @param array $messages
     * @return ExpoResponse
     */
    public function post(array $messages)
    {
        [$compressed, $body] = $this->compress(
            json_encode($messages)
        );

        $headers = $this->getDefaultHeaders();

        if ($compressed) {
            $headers['Content-Encoding'] = 'gzip';
        }

        $response = $this->client->post(self::EXPO_URL, [
            'verify' => false,
            'http_errors' => false,
            'headers' => $headers,
            'body' => $body,
        ]);

        return new ExpoResponse($response);
    }

    /**
     * Set the Expo access token
     *
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Get the clients request headers
     *
     * @return array
     */
    private function getDefaultHeaders()
    {
        $headers = [
            'Host' => 'exp.host',
            'Accept' => 'application/json',
            'Accept-Encoding' => 'gzip, deflate',
            'Content-Type' => 'application/json',
        ];

        if ($this->accessToken) {
            $headers['Authorization'] = "Bearer {$this->accessToken}";
        }

        return $headers;
    }

    /**
     * Compresses a string if > 1kib in size
     *
     * @param string $value
     * @return array
     */
    private function compress(string $value)
    {
        $compressed = false;

        if ((strlen($value) / 1024) >= 1) {
            // returns false if compression fails
            $value = gzencode($value, 6) ?? $value;
            $compressed = true;
        }

        return [$compressed, $value];
    }
}
