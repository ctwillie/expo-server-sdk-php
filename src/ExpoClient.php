<?php

namespace ExpoSDK\Expo;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ExpoClient
{
    const EXPO_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * @var GuzzleHttpClient
     */
    private $guzzle;

    public function __construct()
    {
        $this->guzzle = new Client();
    }

    /**
     * Send messages to the Expo api
     *
     * @param array $messages
     * @return ResponseInterface
     */
    public function post(array $messages)
    {
        return $this->guzzle->post(self::EXPO_URL, [
            'verify' => false,
            'headers' => $this->getHeaders(),
            'json' => $messages,
        ]);
    }

    /**
     * Get the clients default headers
     *
     * @return array
     */
    private function getHeaders()
    {
        return [
            'Host' => 'exp.host',
            'Accept' => 'application/json',
            'Accept-Encoding' => 'gzip, deflate',
            'Content-Type' => 'application/json',
        ];
    }
}
