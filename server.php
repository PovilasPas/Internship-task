<?php

declare(strict_types=1);

use App\DB\ConnectionManager;
use App\Repository\WordRepository;
use App\Web\Controller\WordController;
use App\Web\Request\Request;
use App\Web\Response\JsonResponse;
use App\Web\Response\Response;
use App\Web\Router\Route;
use App\Web\Router\Router;

require_once 'autoload.php';

$connection = ConnectionManager::getConnection();

$parsed = parse_url($_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

$request = new Request($parsed['path'], $data, $method);

$notFound = new JsonResponse(
    [],
    ['message' => 'Not found.'],
    404
);

$notAllowed = new JsonResponse(
    [],
    ['message' => 'Method not allowed.'],
    405
);

$router = new Router($notFound);

$router->addRoute(
    new Route(
        '/^\/api\/words$/',
        $notAllowed,
        [
            'GET' => function () use ($connection): Response {
                $repo = new WordRepository($connection);
                $controller = new WordController($repo);
                return $controller->list();
            },
            'POST' => function (array $params, Request $request) use ($connection): Response {
                $repo = new WordRepository($connection);
                $controller = new WordController($repo);
                return $controller->create($request);
            }
        ]
    )
);

$router->addRoute(
    new Route(
        '/^\/api\/words\/(\d+)$/',
        $notAllowed,
        [
            'PUT' => function (array $params, Request $request) use ($connection): Response {
                $id = (int) $params[0];
                $repo = new WordRepository($connection);
                $controller = new WordController($repo);
                return $controller->update($id, $request);
            },
            'DELETE' => function (array $params) use ($connection): Response {
                $id = (int) $params[0];
                $repo = new WordRepository($connection);
                $controller = new WordController($repo);
                return $controller->delete($id);
            }
        ]
    )
);

$response = $router->resolveRequest($request);

$response->render();
