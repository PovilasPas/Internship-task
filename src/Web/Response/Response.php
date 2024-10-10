<?php

declare(strict_types=1);

namespace App\Web\Response;

abstract class Response
{
    public function __construct(
        protected array $headers = [],
        protected array $body = [],
        protected StatusCode $code = StatusCode::OK,
    ) {

    }

    public abstract function render(): void;
}
