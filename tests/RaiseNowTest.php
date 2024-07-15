<?php

use Mockery as m;
use GuzzleHttp\Client;
use Fet\RaiseNowApi\RaiseNow;
use PHPUnit\Framework\TestCase;
use Fet\RaiseNowApi\AuthenticationManager;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Fet\RaiseNowApi\Gateway\Organisations as OrganisationsGateway;

class RaiseNowTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected RaiseNow $raiseNow;

    public function setUp(): void
    {
        $guzzleClient = new Client([
            'base_uri' => 'https://www.example.com',
        ]);

        $authenticationManager = m::mock(AuthenticationManager::class);
        $authenticationManager
            ->shouldReceive('getAccessToken')
            ->andReturn('access-token');

        $this->raiseNow = new RaiseNow($guzzleClient, $authenticationManager);
    }

    /** @test */
    public function it_can_obtain_an_access_token()
    {
        $this->assertEquals('access-token', $this->raiseNow->getAccessToken());
    }

    /** @test */
    public function it_returns_the_organisations_gateway()
    {
        $this->assertInstanceOf(OrganisationsGateway::class, $this->raiseNow->getOrganisations());
    }
}
