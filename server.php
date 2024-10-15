<?php

declare(strict_types=1);

use App\Database\ConnectionManager;
use App\Database\QueryBuilder;
use App\Database\QueryWriterFactory;
use App\Dependency\DependencyLoader;
use App\Repository\WordRepository;
use App\Web\Controller\WordController;
use App\Web\Request\Request;
use App\Web\Response\JsonResponse;
use App\Web\Response\Response;
use App\Web\Response\StatusCode;
use App\Web\Router\Route;
use App\Web\Router\Router;

require_once __DIR__ . '/vendor/autoload.php';

header('Access-Control-Allow-Origin: http://localhost:63342');

$connection = ConnectionManager::getConnection();

$parsed = parse_url($_SERVER['REQUEST_URI']);
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

$request = new Request($parsed['path'], $data, $method);

$notFound = new JsonResponse(
    body: ['message' => 'Not found.'],
    code: StatusCode::NOT_FOUND
);

$notAllowed = new JsonResponse(
    body: ['message' => 'Method not allowed.'],
    code: StatusCode::METHOD_NOT_ALLOWED
);

$manager = DependencyLoader::load();

$router = $manager->resolve('Router');

$builder = $manager->resolve('QueryBuilder');

$router->addRoute(
    new Route(
        '/^\/api\/words$/',
        [
            Request::GET => function () use ($connection, $builder): Response {
                $repo = new WordRepository($connection, $builder);
                $controller = new WordController($repo);

                return $controller->list();
            },
            Request::POST => function (array $parameters, Request $request) use ($connection, $builder): Response {
                $repo = new WordRepository($connection, $builder);
                $controller = new WordController($repo);

                return $controller->create($request);
            }
        ]
    )
);

$router->addRoute(
    new Route(
        '/^\/api\/words\/(\d+)$/',
        [
            Request::PUT => function (array $parameters, Request $request) use ($connection, $builder): Response {
                $id = (int) $parameters[0];
                $repo = new WordRepository($connection, $builder);
                $controller = new WordController($repo);

                return $controller->update($id, $request);
            },
            Request::DELETE => function (array $parameters) use ($connection, $builder): Response {
                $id = (int) $parameters[0];
                $repo = new WordRepository($connection, $builder);
                $controller = new WordController($repo);

                return $controller->delete($id);
            }
        ]
    )
);

$response = $router->resolveRequest($request, $notFound, $notAllowed);

$response->render();
