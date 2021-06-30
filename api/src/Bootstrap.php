<?php

namespace App;

use Laminas\Http\Request;
use Laminas\Http\Response;

class Bootstrap
{

    public function run($request = null): Response
    {

        if (!$request instanceof Request) {
            $request = (new Request())
                ->setMethod($_SERVER['REQUEST_METHOD'])
                ->setUri(str_replace($_SERVER['REDIRECT_BASE'] ?? '', '', $_SERVER['REQUEST_URI']));
        }

        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $routeCollector) use ($request) {
            $routeCollector->addRoute('OPTIONS', '/*', function () {
                return [];
            });
            $routeCollector->addRoute('GET', '/providers', function () {
                return [
                    ['id' => 1, 'name' => 'Google Maps'],
                    ['id' => 2, 'name' => 'Open Street Map'],
                ];
            });
            $routeCollector->addRoute('POST', '/search', function () use ($request) {
                $data = json_decode(file_get_contents('php://input'), true);
                if (empty($data)) {
                    return ['error' => 'Address not found'];
                }
                return include_once __DIR__ . '/../config/data/response.php';
            });
        });

        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        $response = new Response();
        $response->setStatusCode(200);
        $response->getHeaders()->addHeaders(['Content-Type' => 'application/json']);

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $response->setStatusCode(404)->setContent(json_encode([
                    'error' => 'Router not found'
                ]));
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                if ($request->getMethod() === 'OPTIONS') {
                    $response->setStatusCode(204);
                } else {
                    $response->setStatusCode(405)->setContent(json_encode([
                        'error' => sprintf('Method not allowed (allowed: %s)', implode(',', $routeInfo[1]))
                    ]));
                }
                break;
            case \FastRoute\Dispatcher::FOUND:
                list($state, $handler, $params) = $routeInfo;
                $data = $handler($params);
                $response->setStatusCode(200)->setContent(json_encode($data));
                break;
        }

        return $response;
    }

}
