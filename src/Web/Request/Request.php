<?php

declare(strict_types=1);

namespace App\Web\Request;

class Request
{
    public const string GET = 'GET';
    public const string POST = 'POST';
    public const string PUT = 'PUT';
    public const string PATCH = 'PATCH';
    public const string DELETE = 'DELETE';

    private readonly string $method;

    public function __construct(
        private readonly string $path,
        private readonly ?array $data,
        string $method,
    ) {
        $this->method = strtoupper($method);
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getData(): ?array {
        return $this->data;
    }
}
