<?php

declare(strict_types=1);

use App\DB\ConnectionManager;
use App\Repository\WordRepository;
use App\Web\Controller\WordController;
use App\Web\Response\Response;
use App\Web\Router\Route;
use App\Web\Router\Router;

require_once 'autoload.php';

$connection = ConnectionManager::getConnection();

$parsed = parse_url($_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

$router = new Router();

$router->addRoute(
    new Route(
        '/^\/api\/words$/',
        [
            'GET' => function () use ($connection): Response {
                $repo = new WordRepository($connection);
                $controller = new WordController($repo);
                return $controller->list();
            },
            'POST' => function ($matches, $data) use ($connection): Response {
                $repo = new WordRepository($connection);
                $controller = new WordController($repo);
                return $controller->create($data);
            }
        ]
    )
);

$router->addRoute(
    new Route(
        '/^\/api\/words\/(\d+)$/',
        [
            'PUT' => function ($matches, $data) use ($connection): Response {
                $id = (int) $matches[0];
                $repo = new WordRepository($connection);
                $controller = new WordController($repo);
                return $controller->update($id, $data);
            },
            'DELETE' => function ($matches) use ($connection): Response {
                $id = (int) $matches[0];
                $repo = new WordRepository($connection);
                $controller = new WordController($repo);
                return $controller->delete($id);
            }
        ]
    )
);

$response = $router->resolveRequest($parsed['path'], $method, $data);

$response->render();
