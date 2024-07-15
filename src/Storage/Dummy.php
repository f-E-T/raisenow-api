<?php

namespace Fet\RaiseNowApi\Storage;

use Fet\RaiseNowApi\Contracts\AuthenticationStorage;

class Dummy implements AuthenticationStorage
{
    public function validate(): bool
    {
        return false;
    }

    public function store(string $accessToken): void
    {
        // do nothing
    }

    public function get(): string
    {
        return '';
    }
}
