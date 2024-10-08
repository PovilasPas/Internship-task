<?php

declare(strict_types=1);

namespace App\Web\Response;

abstract class Response
{
    public function __construct(
        protected array $headers,
        protected array $body,
        protected int $code = 200
    ) {

    }

    public abstract function render(): void;
}
