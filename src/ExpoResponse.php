<?php

namespace ExpoSDK;

use Psr\Http\Message\ResponseInterface;

class ExpoResponse
{
    /** @var array */
    private $response;

    /** @var int */
    private $statusCode;

    /**
     * ExpoResponse constructor
     *
     * @param ResponseInterface $response
     */
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
     *
     * @return bool
     */
    public function ok()
    {
        return $this->getStatusCode() === 200 &&
            ! array_key_exists('errors', $this->response);
    }

    /**
     * Get the http response status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Gets the data from the expo response if it exists
     *
     * @return array|null
     */
    public function getData()
    {
        return $this->ok()
            ? $this->response['data']
            : null;
    }

    /**
     * Gets the errors from the expo response if it exists
     *
     * @return array|null
     */
    public function getErrors()
    {
        return ! $this->ok()
            ? $this->response['errors']
            : null;
    }
}
