<?php

declare(strict_types=1);

namespace App\Web\Router;

use App\Web\Request\Request;
use App\Web\Response\Response;

class Route
{
    private array $handlers;

    public function __construct(
        private readonly string $pattern,
        array $handlers,
    ) {
        $this->handlers = array_change_key_case($handlers, CASE_UPPER);
    }

    public function getMatches(string $path): array
    {
        $matches = [];
        preg_match($this->pattern, $path, $matches);

        return $matches;
    }

    public function handle(array $parameters, Request $request, Response $notAllowed): Response
    {
        $method = $request->getMethod();
        if (!array_key_exists($method, $this->handlers)) {
            return $notAllowed;
        }

        $handler = $this->handlers[$method];

        return $handler($parameters, $request);
    }
}
