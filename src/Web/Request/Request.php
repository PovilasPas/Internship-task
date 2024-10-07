<?php

declare(strict_types=1);

namespace App\Web\Request;

class Request
{
    public function __construct(
        protected string $path,
        protected ?array $data,
        protected string $method
    ) {
        $this->method = strtoupper($this->method);
    }

    public function getPath(): string {
        return $this->path;
    }
}
