<?php

namespace ExpoSDK;

use ExpoSDK\Exceptions\ExpoException;
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

    /** @var Client */
    private $client;

    /** @var ExpoErrorManager */
    private $errors;

    public function __construct()
    {
        $this->client = new Client();

        $this->errors = new ExpoErrorManager();
    }

    /**
     * Sends push notification messages to the Expo api
     */
    public function post(array $messages): ExpoResponse
    {
        $actualMessageCount = $this->getActualMessageCount($messages);
        [$compressed, $body] = $this->compress($messages);
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

        $statusCode = $response->getStatusCode();
        $textBody = (string) $response->getBody();

        if ($statusCode !== 200) {
            throw $this->errors->parseErrorResponse($response);
        }

        $result = json_decode($textBody, true);

        if (is_null($result) || json_last_error() !== JSON_ERROR_NONE) {
            throw $this->errors->getTextResponseError($textBody, $statusCode);
        }

        if (array_key_exists('errors', $result)) {
            throw $this->errors->getErrorFromResult($result, $statusCode);
        }

        if (! is_array($result['data']) || count($result['data']) !== $actualMessageCount) {
            throw new ExpoException(sprintf(
                'Expected Expo to respond with %s %s but received %s',
                $actualMessageCount,
                $actualMessageCount === 1 ? 'ticket' : 'tickets',
                count($result['data'])
            ));
        }

        return new ExpoResponse($response);
    }

    /**
     * Set the Expo access token
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Get the clients request headers
     */
    private function getDefaultHeaders(): array
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
     */
    private function compress(array $value): array
    {
        $value = json_encode($value);
        $compressed = false;

        if ((strlen($value) / 1024) > 1) {
            $value = gzencode($value, 6) ?? $value;
            $compressed = true;
        }

        return [$compressed, $value];
    }

    /**
     * Gets the actual message count
     */
    private function getActualMessageCount(array $messages): int
    {
        return count($messages);
    }
}
