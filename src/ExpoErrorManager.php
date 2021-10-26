<?php

namespace ExpoSDK;

use ExpoSDK\Exceptions\ExpoException;
use Psr\Http\Message\ResponseInterface;

class ExpoErrorManager
{
    /**
     * Parses an error response from expo
     */
    public function parseErrorResponse(ResponseInterface $response): ExpoException
    {
        $textBody = (string) $response->getBody();
        $result = null;

        $result = json_decode($textBody, true);
        if (is_null($result) || json_last_error() !== JSON_ERROR_NONE) {
            return $this->getTextResponseError($textBody, $response->getStatusCode());
        }

        if (! $this->responseHasErrors($result)) {
            return $this->getTextResponseError($textBody, $response->getStatusCode());
        }

        return $this->getErrorFromResult(
            $result,
            $response->getStatusCode()
        );
    }

    /**
     * Constructs an exception from the response text
     */
    public function getTextResponseError(string $errorText, int $statusCode): ExpoException
    {
        return new ExpoException(sprintf(
            "Expo responded with an error with status code: %s: %s",
            $statusCode,
            $errorText
        ), $statusCode);
    }

    /**
     * Returns an exception for the first API error from the expo response
     */
    public function getErrorFromResult(array $response, int $statusCode): ExpoException
    {
        if (! $this->responseHasErrors($response)) {
            return new ExpoException(
                'Expected at least one error from Expo. Found none',
                $statusCode
            );
        }

        $error = $response['errors'][0];
        $message = $error['message'];
        $code = $error['code'];

        if (is_string($code)) {
            $message = "{$code}: {$message}";
            $code = $statusCode;
        }

        return new ExpoException($message, $code);
    }

    /**
     * Determine if the json decoded response has errors present
     */
    public function responseHasErrors(array $response): bool
    {
        return array_key_exists('errors', $response) &&
            is_array($response['errors']) &&
            count($response['errors']) > 0;
    }
}
