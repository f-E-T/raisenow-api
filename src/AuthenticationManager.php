<?php

namespace Fet\RaiseNowApi;

use Fet\RaiseNowApi\Contracts\AuthenticationStorage;
use Fet\RaiseNowApi\Gateway\Authentication as AuthenticationGateway;

class AuthenticationManager
{
    public function __construct(
        protected AuthenticationGateway $authentication,
        protected AuthenticationStorage $storage,
    ) {
    }

    public function getAccessToken(): string
    {
        if ($this->storage->validate() === false) {
            $this->storage->store($this->authentication->authenticate()->getAccessToken());
        }

        return $this->storage->get();
    }
}
