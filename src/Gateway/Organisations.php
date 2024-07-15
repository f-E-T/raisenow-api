<?php

namespace Fet\RaiseNowApi\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Fet\RaiseNowApi\Response;

class Organisations extends AbstractGateway
{
    public function __construct(
        protected Client $guzzleClient,
        protected string $accessToken,
    ) {}

    public function list(): Response
    {
        return $this->makeRequest('/organisations');
    }

    public function get(string $organisationUuid): Response
    {
        return $this->makeRequest("/organisations/$organisationUuid");
    }

    public function addresses(string $organisationUuid): Response
    {
        return $this->makeRequest("/organisations/$organisationUuid/addresses");
    }

    public function address(string $organisationUuid, string $addressUuid): Response
    {
        return $this->makeRequest("/organisations/$organisationUuid/addresses/$addressUuid");
    }

    public function metadata(string $organisationUuid): Response
    {
        return $this->makeRequest("/organisations/$organisationUuid/metadata");
    }

    protected function makeRequest(string $uri): Response
    {
        try {
            $guzzleResponse = $this->guzzleClient->get(
                $uri,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->accessToken,
                        'Accept' => 'application/json',
                    ],
                ],
            );
        } catch (BadResponseException $e) {
            return $this->errorResponse($e);
        }

        return $this->getResponse($guzzleResponse);
    }
}
