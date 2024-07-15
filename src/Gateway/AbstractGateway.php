<?php

namespace Fet\RaiseNowApi\Gateway;

use Fet\RaiseNowApi\Exception\Response as ResponseException;
use Fet\RaiseNowApi\Response;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\ResponseInterface as GuzzleResponse;

abstract class AbstractGateway
{
    protected function errorResponse(BadResponseException $e): Response
    {
        $response = $e->getResponse();
        $code = $response->getStatusCode();
        $body = (array) json_decode($response->getBody(), true);

        return new Response($code, $body);
    }

    protected function getResponse(GuzzleResponse $guzzleResponse): Response
    {
        $this->validateResponse($guzzleResponse);

        $code = $guzzleResponse->getStatusCode();
        $body = (array) json_decode($guzzleResponse->getBody(), true);

        return new Response($code, $body);
    }

    protected function validateResponse(GuzzleResponse $guzzleResponse): void
    {
        $this->validateContentType($guzzleResponse);
    }

    protected function validateContentType(GuzzleResponse $guzzleResponse): void
    {
        $contentType = $guzzleResponse->getHeader('Content-Type')[0] ?? '';

        if ($contentType !== 'application/json') {
            throw new ResponseException("Invalid content type $contentType");
        }
    }
}
