<?php

namespace Fet\RaiseNowApi\Gateway;

use GuzzleHttp\Client;
use Fet\RaiseNowApi\Response;
use GuzzleHttp\Exception\BadResponseException;

class Authentication extends AbstractGateway
{
    public function __construct(
        protected Client $guzzleClient,
        protected string $clientId,
        protected string $clientSecret,
    ) {
    }

    public function authenticate(): Response
    {
        try {
            $guzzleResponse = $this->guzzleClient->post(
                '/oauth2/token',
                [
                    'form_params' => [
                        'grant_type' => 'client_credentials',
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                    ],
                ],
            );
        } catch (BadResponseException $e) {
            return $this->errorResponse($e);
        }

        return $this->getResponse($guzzleResponse);
    }
}
