<?php

namespace Omadonex\LaravelSupport\Traits\Tests;

use App\User;

trait RoutesTestTrait
{
    public function testRoutes()
    {
        $config = require_once base_path($this->configPath);
        $routesData = $this->getRoutesData();
        $this->assertTrue($this->checkRoutes($routesData, $config));
    }

    public function getRoutesData()
    {
        $router = app('router');
        $routes = $router->getRoutes();

        $routesNamePrefix = $this->module;
        $posWebRouteNamePart = strlen($routesNamePrefix) + 1;
        $apiVersion = property_exists($this, 'apiVersion') ? $this->apiVersion : 'v1';
        $routesApiNamePrefix = "api.{$apiVersion}.{$this->module}";
        $posApiRouteNamePart = strlen($routesApiNamePrefix) + 1;

        $excludedRouteNames = [];
        $excluded = property_exists($this, 'excluded') ? $this->excluded : [];
        $excludedApi = property_exists($this, 'excludedApi') ? $this->excludedApi : [];
        foreach ($excluded as $partRouteName) {
            $excludedRouteNames[] = "{$routesNamePrefix}.{$partRouteName}";
        }
        foreach ($excludedApi as $partRouteName) {
            $excludedRouteNames[] = "{$routesApiNamePrefix}.{$partRouteName}";
        }

        $routesData = [];
        foreach ($routes as $route) {
            $routeName = $route->getName();
            if (!in_array($routeName, $excludedRouteNames)) {
                $posWeb = strpos($routeName, $routesNamePrefix);
                $posApi = strpos($routeName, $routesApiNamePrefix);
                if (($posWeb === 0) || ($posApi === 0)) {
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
                    $isApi = $posApi === 0;

                    $routeData = [
                        'name' => $routeName,
                        'namePart' => substr($routeName, $isApi ? $posApiRouteNamePart : $posWebRouteNamePart),
                        'method' => $route->methods()[0],
                        'parameters' => $route->parameterNames(),
                        'authType' => $authType,
                        'aclOn' => $aclOn,
                        'isApi' => $isApi,
                    ];

                    if ($aclOn) {
                        $routeData['acl'] = [
                            'roles' => $route->getAction('roles'),
                            'privileges' => $route->getAction('privileges'),
                        ];
                    }

                    if ($routeData['method'] === 'PUT')
                        $routesData[] = $routeData;
                }
            }
        }

        return $routesData;
    }

    public function createModelByParameter($parameter)
    {

    }

    public function getConfigMeta($config, $routeData)
    {
        if ($routeData['isApi']) {
            return $config['requests']['api'][$routeData['namePart']];
        }

        return $config['requests']['web'][$routeData['namePart']];
    }

    public function splitData($data) {
        $dataM = [];

        foreach ($data as $key => $value) {
            if ((substr($key, 0, 2) !== '__') && ($key !== 't')) {
                $dataM[$key] = $value;
            }
        }

        return [
            'data' => $dataM,
            'dataT' => $data['t'],
        ];
    }

    public function checkRoutes($routesData, $config)
    {
        $user = factory(User::class)->create();

        $failed = false;

        echo PHP_EOL;
        foreach ($routesData as $routeData) {
            $routeName = $routeData['name'];

            switch ($routeData['method']) {
                case 'PUT':
                    //только update методы, поэтому необходимо иметь модель
                    $configMeta = $this->getConfigMeta($config, $routeData);
                    $createKey = $configMeta['create'][0];
                    $createMeta = $config['createData'][$createKey];

                    $service = resolve($createMeta['service']);
                    $translatable = array_key_exists('translatable', $createMeta) ? $createMeta['translatable'] : false;
                    $createData = array_key_exists('data', $createMeta) ? $createMeta['data'] : $config['modelData'][$createKey];

                    if ($translatable) {
                        $createDataSplitted = $this->splitData($createData);
                        $model = $service->createT($createDataSplitted['data'], $createDataSplitted['dataT']);
                    } else {
                        $model = $service->create($createData);
                    }

                    $url = route($routeName, [$routeData['parameters'][0] => $model->id]);
                    $response = $this->put($url.'?api_token='.$user->api_token, $createData);
                    echo $response->status();
                    break;
                case 'DELETE':
                    break;
            }
            /*
            $parameters = [];
            if (count($routeData['parameters'])) {
                foreach ($routeData['parameters'] as $parameter) {
                    if ($routeData['methods'][0] === 'DELETE') {

                    }
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
            */
        }

        return !$failed;
    }
}
