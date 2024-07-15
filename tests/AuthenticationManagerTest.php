<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Fet\RaiseNowApi\AuthenticationManager;
use Fet\RaiseNowApi\Gateway\Authentication;
use Fet\RaiseNowApi\Response as RaiseNowResponse;
use Fet\RaiseNowApi\Contracts\AuthenticationStorage;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class AuthenticationManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function it_returns_an_authentication_token_from_gateway()
    {
        $authentication = m::mock(Authentication::class);
        $authentication
            ->shouldReceive('authenticate')
            ->once()
            ->andReturn(new RaiseNowResponse(200, ['access_token' => 'access-token']));

        $authenticationStorage = m::mock(AuthenticationStorage::class);
        $authenticationStorage->shouldReceive('validate')->once()->andReturn(false);
        $authenticationStorage->shouldReceive('store')->once()->with('access-token');
        $authenticationStorage->shouldReceive('get')->once()->andReturn('access-token');

        $authenticationManager = new AuthenticationManager($authentication, $authenticationStorage);
        $this->assertEquals('access-token', $authenticationManager->getAccessToken());
    }

    /** @test */
    public function it_returns_an_authentication_token_from_storage()
    {
        $authentication = m::mock(Authentication::class);
        $authentication->shouldNotReceive('authenticate');

        $authenticationStorage = m::mock(AuthenticationStorage::class);
        $authenticationStorage->shouldReceive('validate')->once()->andReturn(true);
        $authenticationStorage->shouldNotReceive('store');
        $authenticationStorage->shouldReceive('get')->once()->andReturn('access-token');

        $authenticationManager = new AuthenticationManager($authentication, $authenticationStorage);
        $this->assertEquals('access-token', $authenticationManager->getAccessToken());
    }
}
