<?php

use Mockery as m;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Fet\RaiseNowApi\Storage\Dummy as DummyStorage;
use Fet\RaiseNowApi\Contracts\AuthenticationStorage;
use Fet\RaiseNowApi\Storage\Factory as StorageFactory;

class StorageFactoryTest extends TestCase
{
    /** @test */
    public function it_can_create_a_storage()
    {
        $this->assertInstanceOf(DummyStorage::class, StorageFactory::create());

        $this->assertInstanceOf(
            AuthenticationStorage::class,
            StorageFactory::create(['storage_class' => m::mock(AuthenticationStorage::class)])
        );

        $this->expectException(InvalidArgumentException::class);
        StorageFactory::create(['storage_class' => 'invalid']);
    }
}
