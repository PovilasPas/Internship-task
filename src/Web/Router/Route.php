<?php

declare(strict_types=1);

namespace App\Web\Router;

use App\Web\Response\JsonResponse;
use App\Web\Response\Response;

class Route
{
    private array $handlers;

    public function __construct(
        private string $pattern,
        array $handlers
    ) {
        $this->handlers = [];
        foreach ($handlers as $key => $handler) {
            $this->handlers[strtoupper($key)] = $handler;
        }
    }

    public function getMatches(string $path): array
    {
        $matches = [];
        preg_match($this->pattern, $path, $matches);
        return $matches;
    }

    public function handle(string $method, array $params, ?array $data): Response
    {
        $method = strtoupper($method);
        if (!array_key_exists($method, $this->handlers)) {
            return new JsonResponse(
              [],
              ['message' => 'Method not allowed.'],
              405
            );
        }
        $handler = $this->handlers[$method];
        return $handler($params, $data);
    }
}
