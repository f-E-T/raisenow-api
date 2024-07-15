<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use Fet\RaiseNowApi\Gateway\Authentication;
use Fet\RaiseNowApi\Response as RaiseNowResponse;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Fet\RaiseNowApi\Exception\Response as ResponseException;

class AuthenticationTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    
    /** @test */
    public function it_can_obtain_a_bearer_token()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $response200 = '{"token_type":"token-type","expires_in":3600,"access_token": "access-token"}'),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $guzzleClient = new Client([
            'base_uri' => 'https://www.example.com',
            'handler' => $handlerStack,
        ]);

        $authentication = new Authentication($guzzleClient, 'client-id', 'client-secret');
        $authenticationResponse = $authentication->authenticate();

        $lastRequest = $mock->getLastRequest();
        $formParams = [];
        parse_str($lastRequest->getBody(), $formParams);

        $this->assertEquals('https', $lastRequest->getUri()->getScheme());
        $this->assertEquals('www.example.com', $lastRequest->getUri()->getHost());
        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals('/oauth2/token', $lastRequest->getRequestTarget());

        $this->assertEquals('client_credentials', $formParams['grant_type']);
        $this->assertEquals('client-id', $formParams['client_id']);
        $this->assertEquals('client-secret', $formParams['client_secret']);

        $this->assertInstanceOf(RaiseNowResponse::class, $authenticationResponse);
        $this->assertEquals(200, $authenticationResponse->getCode());
        $this->assertEquals(json_decode($response200, true), $authenticationResponse->getBody());
    }

    /** @test */
    public function it_handles_error_responses()
    {
        $mock = new MockHandler([
            new Response(400, [], $response400 = '{"error":"unsupported_grant_type","error_description":"The authorization grant type is not supported by the authorization server.","hint": "Check that all required parameters have been provided","message": "The authorization grant type is not supported by the authorization server."}'),
            new Response(401, [], $response401 = '{"error":"invalid_client","error_description":"Client authentication failed","message":"Client authentication failed"}'),
            new Response(200, ['Content-Type' => 'text/html'], ''),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $guzzleClient = new Client([
            'base_uri' => 'https://www.example.com',
            'handler' => $handlerStack,
        ]);

        $authentication = new Authentication($guzzleClient, 'client-id', 'client-secret');

        $authenticationResponse = $authentication->authenticate();
        $this->assertInstanceOf(RaiseNowResponse::class, $authenticationResponse);
        $this->assertEquals(400, $authenticationResponse->getCode());
        $this->assertEquals(json_decode($response400, true), $authenticationResponse->getBody());

        $authenticationResponse = $authentication->authenticate();
        $this->assertInstanceOf(RaiseNowResponse::class, $authenticationResponse);
        $this->assertEquals(401, $authenticationResponse->getCode());
        $this->assertEquals(json_decode($response401, true), $authenticationResponse->getBody());

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Invalid content type text/html');
        $authentication->authenticate();
    }
}
