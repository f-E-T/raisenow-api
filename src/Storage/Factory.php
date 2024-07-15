<?php

namespace Fet\RaiseNowApi\Storage;

use InvalidArgumentException;
use Fet\RaiseNowApi\Contracts\AuthenticationStorage;

class Factory
{
    /**
     * @param array<string, string> $config
     */
    public static function create(array $config = []): AuthenticationStorage
    {
        $storageClass = $config['storage_class'] ?? Dummy::class;

        if (!is_a($storageClass, AuthenticationStorage::class, true)) {
            throw new InvalidArgumentException(sprintf('The storage class %s must implement %s.', $storageClass, AuthenticationStorage::class));
        }

        return new $storageClass;
    }
}
