<?php

namespace ExpoSDK;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ExpoResponseTest extends TestCase
{
    /** @var array */
    private $data = [
        [
            'status' => 'ok',
            'id' => 'xxxxxx-xxxxx-xxxxx-xxxxxxx',
        ],
    ];

    /** @test */
    public function expo_response_will_instantiate()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'data' => $this->data,
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handlerStack,
            'http_errors' => false,
        ]);

        $response = $client->request('GET', '/');

        $this->assertInstanceOf(
            ResponseInterface::class,
            $response
        );

        return new ExpoResponse($response);
    }

    /**
     * @test
     * @depends expo_response_will_instantiate
     */
    public function can_determine_if_response_is_ok(ExpoResponse $response)
    {
        $this->assertTrue(
            $response->ok()
        );
    }

    /**
     * @test
     * @depends expo_response_will_instantiate
     */
    public function can_retrieve_status_code_for_response(ExpoResponse $response)
    {
        $this->assertSame(
            200,
            $response->getStatusCode()
        );
    }

    /**
     * @test
     * @depends expo_response_will_instantiate
     */
    public function can_retrieve_data_from_successsful_response(ExpoResponse $response)
    {
        $this->assertSame(
            $this->data,
            $response->getData()
        );
    }
}
