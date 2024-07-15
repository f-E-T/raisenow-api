<?php

namespace Fet\RaiseNowApi;

use Illuminate\Support\Str;

/**
 * @method getAccessToken()
 */
class Response
{
    const SUCCESS_STATUS_CODE = 200;

    /**
     * @param array<mixed> $body
     */
    public function __construct(
        protected int $code,
        protected array $body
    ) {
    }

    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return array<mixed>
     */
    public function getBody(): array
    {
        return $this->body;
    }

    public function isSuccess(): bool
    {
        return $this->getCode() === self::SUCCESS_STATUS_CODE;
    }

    /**
     * @param array<mixed> $arguments
     */
    public function __call(string $name, $arguments): mixed
    {
        if (preg_match('/^get(.+)$/', $name, $matches)) {
            $attribute = Str::snake($matches[1]);

            return $this->body[$attribute] ?? false;
        }

        return false;
    }
}
