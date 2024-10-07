<?php

declare(strict_types=1);

namespace App\Web\Router;

use App\Web\Response\JsonResponse;
use App\Web\Response\Response;

class Router
{
    private array $routes = [];

    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function resolveRequest(string $path, string $method, ?array $data): Response
    {
        foreach ($this->routes as $route) {
            $matches = $route->getMatches($path);
            if (!empty($matches)) {
                $matches = array_slice($matches, 1);
                return $route->handle($method, $matches, $data);
            }
        }
        return new JsonResponse(
            [],
            ['message' => 'Not found.'],
            404
        );
    }
}
