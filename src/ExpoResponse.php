<?php

namespace ExpoSDK;

use Psr\Http\Message\ResponseInterface;

class ExpoResponse
{
    /** @var array */
    private $response;

    /** @var int */
    private $statusCode;

    public function __construct(ResponseInterface $response)
    {
        $this->response = json_decode(
            $response->getBody(),
            true
        );

        $this->statusCode = $response->getStatusCode();
    }

    /**
     * Checks if the request succeeded
     */
    public function ok(): bool
    {
        return $this->getStatusCode() === 200 &&
            ! array_key_exists('errors', $this->response);
    }

    /**
     * Get the http response status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Gets the data from the expo response
     *
     * @return array|null
     */
    public function getData()
    {
        return $this->ok()
            ? $this->response['data']
            : null;
    }
}
