<?php

namespace Omadonex\LaravelSupport\Traits\Tests;

use App\User;

trait RoutesTestTrait
{
    public function testRoutes()
    {
        $routesData = $this->getRoutesData();
        $this->assertTrue($this->checkRoutes($routesData));
    }

    public function getRoutesData()
    {
        $router = app('router');
        $routes = $router->getRoutes();

        $routesData = [];
        foreach ($routes as $route) {
            if (strpos($route->uri, '_debugbar') === false) {
                if (!count(array_intersect(['POST', 'PUT', 'PATCH', 'DELETE'], $route->methods()))) {
                    $middleware = $route->gatherMiddleware();

                    if (in_array('auth', $middleware)) {
                        $authType = 1;
                    } elseif (in_array('auth:api', $middleware)) {
                        $authType = 2;
                    } elseif (in_array('guest', $middleware)) {
                        $authType = -1;
                    } else {
                        $authType = 0;
                    }

                    $aclOn = in_array('acl', $middleware);

                    $routeData = [
                        'name' => $route->getName(),
                        'parameters' => $route->parameterNames(),
                        'authType' => $authType,
                        'aclOn' => $aclOn,
                    ];

                    if ($aclOn) {
                        $routeData['acl'] = [
                            'roles' => $route->getAction('roles'),
                            'privileges' => $route->getAction('privileges'),
                        ];
                    }

                    $routesData[] = $routeData;
                }
            }
        }

        return $routesData;
    }

    public function checkRoutes($routesData)
    {
        $user = factory(User::class)->create();

        $failed = false;

        echo PHP_EOL;
        foreach ($routesData as $routeData) {
            $routeName = $routeData['name'];
            $parameters = [];
            if (count($routeData['parameters'])) {
                foreach ($routeData['parameters'] as $parameter) {
                    /*
                    switch ($parameter) {
                        case 'license':
                            break;
                    }*/
                    $parameters[$parameter] = '7';
                }
            }

            $url = route($routeName, $parameters);

            if ($routeData['aclOn']) {
                $user->roles()->sync($routeData['acl']['roles']);
                $user->privileges()->sync($routeData['acl']['privileges']);
            }

            switch ($routeData['authType']) {
                case 1:
                    $response = $this->actingAs($user)->get($url);
                    break;
                case 2:
                    $response = $this->get($url.'?api_token='.$user->api_token);
                    break;
                case 0:
                    $response = $this->get($url);
                    break;
                default:
                    auth()->logout();
                    $response = $this->get($url);
            }

            $status = $response->status();
            if ($status !== 200) {
                echo "(FAILED {$status}) - '{$routeName}'";
                $failed = true;
                echo PHP_EOL;
            }
        }

        return !$failed;
    }
}
