<?php

namespace Fet\RaiseNowApi\Contracts;

interface AuthenticationStorage
{
    public function store(string $accessToken): void;

    public function get(): string;

    public function validate(): bool;
}
