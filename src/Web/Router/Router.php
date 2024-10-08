<?php

declare(strict_types=1);

namespace App\Web\Router;

use App\Web\Request\Request;
use App\Web\Response\Response;

class Router
{
    private array $routes = [];

    public function __construct(
        private readonly Response $notFound,
    ) {

    }

    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function resolveRequest(Request $request): Response
    {
        foreach ($this->routes as $route) {
            $matches = $route->getMatches($request->getPath());
            if (!empty($matches)) {
                $matches = array_slice($matches, 1);
                return $route->handle($matches, $request);
            }
        }
        return $this->notFound;
    }
}
