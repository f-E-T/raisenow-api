<?php

namespace Fet\RaiseNowApi;

use GuzzleHttp\Client;
use Fet\RaiseNowApi\Gateway\Organisations as OrganisationsGateway;
use Fet\RaiseNowApi\AuthenticationManager;
use Fet\RaiseNowApi\Gateway\Authentication as AuthenticationGateway;
use Fet\RaiseNowApi\Storage\Factory as StorageFactory;

class RaiseNow
{
    protected string $accessToken;

    public function __construct(
        protected Client $guzzleClient,
        protected AuthenticationManager $authenticationManager,
    ) {
        $this->accessToken = $authenticationManager->getAccessToken();
    }

    public function getOrganisations(): OrganisationsGateway
    {
        return new OrganisationsGateway($this->guzzleClient, $this->getAccessToken());
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param array<string, string> $config
     */
    public static function create(array $config): self
    {
        $guzzleClient = new Client([
            'base_uri' => $config['uri'],
        ]);

        $authenticationGateway = new AuthenticationGateway($guzzleClient, $config['client_id'], $config['client_secret']);
        $storage = StorageFactory::create($config);
        $authenticationManager = new AuthenticationManager($authenticationGateway, $storage);

        return new self($guzzleClient, $authenticationManager);
    }
}
