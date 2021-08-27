<?php

namespace ExpoSDK;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

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
     * Send messages to the Expo api
     *
     * @param array $messages
     * @return ResponseInterface
     */
    public function post(array $messages)
    {
        return $this->client->post(self::EXPO_URL, [
            'verify' => false,
            'headers' => $this->getHeaders(),
            'json' => $messages,
        ]);
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
    private function getHeaders()
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
}
