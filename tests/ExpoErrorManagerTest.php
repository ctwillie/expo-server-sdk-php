<?php

namespace ExpoSDK\Tests;

use ExpoSDK\Exceptions\ExpoException;
use ExpoSDK\ExpoErrorManager;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ExpoErrorManagerTest extends TestCase
{
    /** @var array */
    private $errorResponse = [
        'errors' => [
            [
                'message' => 'Some expo error',
                'code' => 400,
            ],
        ],
    ];

    /** @test */
    public function response_has_errors_can_identify_errors_present()
    {
        $errors = new ExpoErrorManager();

        $this->assertTrue(
            $errors->responseHasErrors($this->errorResponse)
        );

        $this->assertFalse(
            $errors->responseHasErrors([
                'errors' => [],
            ])
        );
    }

    /** @test */
    public function get_text_response_error_returns_exception()
    {
        $errors = new ExpoErrorManager();

        $exception = $errors->getTextResponseError(
            'Expo error message',
            400
        );

        $this->assertInstanceOf(
            ExpoException::class,
            $exception
        );

        $this->assertSame(400, $exception->getCode());
    }

    /** @test */
    public function get_error_from_result_returns_exception()
    {
        $errors = new ExpoErrorManager();

        $exception = $errors->getErrorFromResult(
            $this->errorResponse,
            400
        );

        $this->assertInstanceOf(
            ExpoException::class,
            $exception
        );
        $this->assertSame(
            $this->errorResponse['errors'][0]['message'],
            $exception->getMessage()
        );
        $this->assertSame(400, $exception->getCode());
    }

    /** @test */
    public function get_error_from_result_throws_exception_with_no_errors()
    {
        $errorResponse = [
            'errors' => [],
        ];

        $errors = new ExpoErrorManager();

        $exception = $errors->getErrorFromResult(
            $errorResponse,
            400
        );

        $this->assertInstanceOf(
            ExpoException::class,
            $exception
        );
        $this->assertSame(
            'Expected at least one error from Expo. Found none',
            $exception->getMessage()
        );
    }

    /** @test */
    public function can_parse_an_error_response_and_return_exception()
    {
        $mock = new MockHandler([
            new Response(500, [], json_encode($this->errorResponse)),
            new Response(500, [], json_encode([])),
            new Response(500, [], "\xE1\xE9\xF3\xFA"),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handlerStack,
            'http_errors' => false,
        ]);

        $errors = new ExpoErrorManager();

        $response = $client->request('GET', '/');
        $exception = $errors->parseErrorResponse($response);

        $this->assertInstanceOf(
            ExpoException::class,
            $exception
        );

        $response = $client->request('GET', '/');
        $exception = $errors->parseErrorResponse($response);

        $this->assertInstanceOf(
            ExpoException::class,
            $exception
        );

        $response = $client->request('GET', '/');
        $exception = $errors->parseErrorResponse($response);

        $this->assertInstanceOf(
            ExpoException::class,
            $exception
        );
    }
}
