<?php

declare(strict_types=1);

namespace App\Web\Router;

use App\Web\Request\Request;
use App\Web\Response\Response;

class Route
{
    private array $handlers = [];

    public function __construct(
        private readonly string $pattern,
        private readonly Response $notAllowed,
        array $handlers,
    ) {
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

    public function handle(array $params, Request $request): Response
    {
        $method = $request->getMethod();
        if (!array_key_exists($method, $this->handlers)) {
            return $this->notAllowed;
        }
        $handler = $this->handlers[$method];
        return $handler($params, $request);
    }
}
