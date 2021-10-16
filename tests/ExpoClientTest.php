<?php

use ExpoSDK\Exceptions\ExpoException;
use ExpoSDK\ExpoClient;
use ExpoSDK\ExpoMessage;
use ExpoSDK\ExpoResponse;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ExpoClientTest extends TestCase
{
    /** @test */
    public function can_send_push_notifications()
    {
        $data = [
            [
                "id" => "xxx-xxxx-xxxxx-xxxx",
                "status" => "ok",
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'data' => $data,
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new ExpoClient([
            'handler' => $handlerStack,
            'http_errors' => false,
        ]);
        $client->setAccessToken('secret');

        $message = (new ExpoMessage([
            'title' => 'Title',
            'to' => 'ExpoPushToken[xxxx]',
        ]))->toArray();

        $response = $client->sendPushNotifications([$message]);

        $this->assertInstanceOf(
            ExpoResponse::class,
            $response
        );
    }

    /** @test */
    public function throws_exception_if_receipt_count_doesnt_match_ticket_count()
    {
        $data = [
            [
                "id" => "xxx-xxxx-xxxxx-xxxx",
                "status" => "ok",
            ],
            [
                "id" => "yyy-yyyy-yyyyy-yyyy",
                "status" => "ok",
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'data' => $data,
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new ExpoClient([
            'handler' => $handlerStack,
            'http_errors' => false,
        ]);

        $message = (new ExpoMessage([
            'title' => 'Title',
            'to' => 'ExpoPushToken[xxxx]',
        ]))->toArray();

        $this->expectExceptionMessage(
            'Expected Expo to respond with 1 ticket but received 2'
        );

        $client->sendPushNotifications([$message]);
    }

    /** @test */
    public function throws_exception_if_response_status_code_is_not_200()
    {
        $mock = new MockHandler([
            new Response(500),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new ExpoClient([
            'handler' => $handlerStack,
            'http_errors' => false,
        ]);

        $message = (new ExpoMessage([
            'title' => 'Title',
            'to' => 'ExpoPushToken[xxxx]',
        ]))->toArray();

        $this->expectException(ExpoException::class);

        $client->sendPushNotifications([$message]);
    }

    /** @test */
    public function an_access_token_can_be_set()
    {
        $token = 'secret';
        $client = new ExpoClient();
        $client->setAccessToken($token);

        $reflectedClass = new \ReflectionClass($client);
        $reflection = $reflectedClass->getProperty('accessToken');
        $reflection->setAccessible(true);

        $this->assertEquals(
            $token,
            $reflection->getValue($client)
        );
    }

    /** @test */
    public function compresses_request_body_if_too_large()
    {
        $data = [
            [
                "id" => "xxx-xxxx-xxxxx-xxxx",
                "status" => "ok",
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'data' => $data,
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new ExpoClient([
            'handler' => $handlerStack,
            'http_errors' => false,
        ]);

        $d = [];
        for ($i = 0; $i < 1000; $i++) {
            // make an associative array
            $d[$i.$i] = $i;
        }

        $message = (new ExpoMessage([
            'title' => 'Title',
            'to' => 'ExpoPushToken[xxxx]',
            'data' => $d,
        ]))->toArray();

        $response = $client->sendPushNotifications([$message]);

        $this->assertInstanceOf(ExpoResponse::class, $response);
    }

    /** @test */
    public function throws_exception_if_response_has_errors()
    {
        $data = [
            [
                "code" => 400,
                "message" => 'oops',
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'errors' => $data,
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new ExpoClient([
            'handler' => $handlerStack,
            'http_errors' => false,
        ]);
        $client->setAccessToken('secret');

        $message = (new ExpoMessage([
            'title' => 'Title',
            'to' => 'ExpoPushToken[xxxx]',
        ]))->toArray();

        $this->expectException(ExpoException::class);

        $client->sendPushNotifications([$message]);
    }
}
