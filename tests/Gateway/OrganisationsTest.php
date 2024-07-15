<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use Fet\RaiseNowApi\Gateway\Organisations;
use Fet\RaiseNowApi\Response as RaiseNowResponse;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Fet\RaiseNowApi\Exception\Response as ResponseException;

class OrganisationsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected $mock;

    /** @test */
    public function it_returns_a_list_of_organisations()
    {
        $response = '[
            {
                "uuid": "d4aabab9-3765-4726-bca3-0da1d88bdeaa",
                "name": "Futurogoal",
                "created": "1454186729",
                "approval_status": "approved",
                "active": true
            },
            {
                "uuid": "d4aabab9-3725-4726-bca3-0da1d88bdeaa",
                "name": "Sample Organisation",
                "created": "1454185729",
                "approval_status": "approval_pending",
                "active": false
            }
        ]';

        $organisations = $this->getOrganisations($response);
        $organisationResponse = $organisations->list();

        $lastRequest = $this->mock->getLastRequest();

        $this->defaultAsserts($lastRequest, $organisationResponse, $response);

        $this->assertEquals('/organisations', $lastRequest->getRequestTarget());
    }

    /** @test */
    public function it_returns_a_single_organisation()
    {
        $organisationUuid = 'd4aabab9-3765-4726-bca3-0da1d88bdeaa';
        $response = '[
            {
                "uuid": "' . $organisationUuid . '",
                "name": "Sample Organisation",
                "created": "1454186729",
                "active": true,
                "approval_status": "approved",
                "website_url": "https://raisenow.com",
                "merchant_category_code": "8699",
                "locale": "de_CH",
                "accounts": [
                    {
                        "uuid": "e6f92b16-da43-44de-9b59-4759a52e0b3b"
                    }
                ]
            }
        ]';

        $organisations = $this->getOrganisations($response);
        $organisationResponse = $organisations->get($organisationUuid);

        $lastRequest = $this->mock->getLastRequest();

        $this->defaultAsserts($lastRequest, $organisationResponse, $response);

        $this->assertEquals("/organisations/$organisationUuid", $lastRequest->getRequestTarget());
    }

    /** @test */
    public function it_returns_a_list_of_organisation_addresses()
    {
        $organisationUuid = 'c6f92b16-da43-44de-9b59-4759a52e0b3c';
        $response = '[
            {
                "uuid": "c6f92b16-da43-44de-9b59-4759a52e0b3c",
                "created": 1530097351,
                "address_line1": "Finance",
                "address_line2": "221B Baker St",
                "postal_code": "NW1 6XE",
                "city": "London",
                "country_code": "UK",
                "type": "billing"
            }
        ]';

        $organisations = $this->getOrganisations($response);
        $organisationResponse = $organisations->addresses($organisationUuid);

        $lastRequest = $this->mock->getLastRequest();

        $this->defaultAsserts($lastRequest, $organisationResponse, $response);

        $this->assertEquals("/organisations/$organisationUuid/addresses", $lastRequest->getRequestTarget());
    }

    /** @test */
    public function it_returns_a_single_organisation_address()
    {
        $organisationUuid = '3c18fb9c-a43d-44fc-8ed2-764168d1d425';
        $addressUuid = 'c6f92b16-da43-44de-9b59-4759a52e0b3c';
        $response = '[
            {
                "uuid": "' . $addressUuid . '",
                "created": 1530097351,
                "address_line1": "Finance",
                "address_line2": "221B Baker St",
                "postal_code": "NW1 6XE",
                "city": "London",
                "country_code": "UK",
                "type": "billing"
            }
        ]';

        $organisations = $this->getOrganisations($response);
        $organisationResponse = $organisations->address($organisationUuid, $addressUuid);

        $lastRequest = $this->mock->getLastRequest();

        $this->defaultAsserts($lastRequest, $organisationResponse, $response);

        $this->assertEquals("/organisations/$organisationUuid/addresses/$addressUuid", $lastRequest->getRequestTarget());
    }

    /** @test */
    public function it_returns_a_list_of_organisation_metadata()
    {
        $organisationUuid = '3c18fb9c-a43d-44fc-8ed2-764168d1d425';

        $response = '[
            {
                "metadata_name_1": "some value",
                "something": "some other value"
            }
        ]';

        $organisations = $this->getOrganisations($response);
        $organisationResponse = $organisations->metadata($organisationUuid);

        $lastRequest = $this->mock->getLastRequest();

        $this->defaultAsserts($lastRequest, $organisationResponse, $response);

        $this->assertEquals("/organisations/$organisationUuid/metadata", $lastRequest->getRequestTarget());
    }

    /** @test */
    public function it_handles_error_responses()
    {
        $mock = new MockHandler([
            new Response(401, [], $response401 = '{"message": "Authentication Required"}'),
            new Response(403, [], $response403 = '{"request_id":"5ca31551a0695","http_code":403,"code":"access_denied"}'),
            new Response(404, [], $response404 = '{"request_id":"5ca31551a0695","http_code":404,"code":"not_found"}'),
            new Response(200, ['Content-Type' => 'text/html'], ''),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $guzzleClient = new Client([
            'base_uri' => 'https://www.example.com',
            'handler' => $handlerStack,
        ]);

        $organisations = new Organisations($guzzleClient, 'access-token');

        $organisationResponse = $organisations->list();
        $this->assertInstanceOf(RaiseNowResponse::class, $organisationResponse);
        $this->assertEquals(401, $organisationResponse->getCode());
        $this->assertEquals(json_decode($response401, true), $organisationResponse->getBody());

        $organisationResponse = $organisations->list();
        $this->assertInstanceOf(RaiseNowResponse::class, $organisationResponse);
        $this->assertEquals(403, $organisationResponse->getCode());
        $this->assertEquals(json_decode($response403, true), $organisationResponse->getBody());

        $organisationResponse = $organisations->list();
        $this->assertInstanceOf(RaiseNowResponse::class, $organisationResponse);
        $this->assertEquals(404, $organisationResponse->getCode());
        $this->assertEquals(json_decode($response404, true), $organisationResponse->getBody());

        $this->expectException(ResponseException::class);
        $this->expectExceptionMessage('Invalid content type text/html');
        $organisations->list();
    }

    protected function getOrganisations(string $response): Organisations
    {
        $this->mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $response),
        ]);

        $handlerStack = HandlerStack::create($this->mock);

        $guzzleClient = new Client([
            'base_uri' => 'https://www.example.com',
            'handler' => $handlerStack,
        ]);

        return new Organisations($guzzleClient, 'access-token');
    }

    protected function defaultAsserts($lastRequest, $response, $expectedResponse): void
    {
        $this->assertEquals($lastRequest->getHeaderLine('Authorization'), 'Bearer access-token');
        $this->assertEquals($lastRequest->getHeaderLine('Accept'), 'application/json');

        $this->assertEquals('https', $lastRequest->getUri()->getScheme());
        $this->assertEquals('www.example.com', $lastRequest->getUri()->getHost());
        $this->assertEquals('GET', $lastRequest->getMethod());

        $this->assertInstanceOf(RaiseNowResponse::class, $response);
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals(json_decode($expectedResponse, true), $response->getBody());
    }
}
