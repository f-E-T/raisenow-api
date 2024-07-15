<?php

use Fet\RaiseNowApi\Response;
use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class ResponseTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function it_can_retrieve_the_status_code()
    {
        $this->assertEquals(200, (new Response(200, []))->getCode());
    }

    /** @test */
    public function it_can_check_for_success()
    {
        $this->assertTrue((new Response(200, []))->isSuccess());
        $this->assertFalse((new Response(500, []))->isSuccess());
    }

    /** @test */
    public function it_can_retrieve_the_body()
    {
        $this->assertEquals(['foo' => 'bar'], (new Response(200, ['foo' => 'bar']))->getBody());
    }

    /** @test */
    public function it_can_access_body_values_via_getters()
    {
        $this->assertEquals('bar', (new Response(200, ['foo' => 'bar']))->getFoo());
        $this->assertFalse((new Response(200, ['foo' => 'bar']))->getUnknown());
        $this->assertFalse((new Response(200, ['foo' => 'bar']))->unknown());
    }
}
