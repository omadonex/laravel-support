<?php

namespace Omadonex\LaravelSupport\Traits\Tests;

use App\User;
use Omadonex\LaravelSupport\Classes\ConstantsCustom;

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
                        $authType = ConstantsCustom::TEST_AUTH_TYPE_SESSION;
                    } elseif (in_array('auth:api', $middleware)) {
                        $authType = ConstantsCustom::TEST_AUTH_TYPE_API;
                    } elseif (in_array('guest', $middleware)) {
                        $authType = ConstantsCustom::TEST_AUTH_TYPE_GUEST;
                    } else {
                        $authType = ConstantsCustom::TEST_AUTH_TYPE_NO_MATTER;
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

                    $routesData[] = $routeData;
                }
            }
        }

        return $routesData;
    }

    public function createModel($config, $key)
    {
        $createMeta = $config['createData'][$key];
        $service = resolve($createMeta['service']);
        $translatable = array_key_exists('translatable', $createMeta) ? $createMeta['translatable'] : false;
        $createData = array_key_exists('data', $createMeta) ? $createMeta['data'] : $config['modelData'][$key];

        if ($translatable) {
            $createDataSplitted = $this->splitData($createData);
            $model = $service->createT($createDataSplitted['data'], $createDataSplitted['dataT']);
        } else {
            $model = $service->create($createData);
        }

        return [
            'model' => $model,
            'data' => $createData,
        ];
    }

    public function getConfigMeta($config, $routeData)
    {
        $data = $routeData['isApi'] ? $config['requests']['api'] : $config['requests']['web'];

        if (array_key_exists($routeData['namePart'], $data)) {
            return $data[$routeData['namePart']];
        }

        return null;
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

    public function sendRequest($method, $url, $data = [])
    {
        if ($method === 'GET') {
            return $this->$method($url);
        }

        return $this->$method($url, $data);
    }

    public function checkRoutes($routesData, $config)
    {
        $user = factory(User::class)->create();

        $failed = false;

        echo PHP_EOL;
        foreach ($routesData as $routeData) {
            $routeName = $routeData['name'];
            $method = $routeData['method'];
            $parameters = [];
            $requestData = [];
            if (count($routeData['parameters'])) {
                foreach ($routeData['parameters'] as $parameter) {
                    if (in_array($parameter, $config['parameters']['create'])) {
                        $result = $this->createModel($config, $parameter);
                        $requestData = $result['data'];
                        $parameters[$parameter] = $result['model']->id;
                    } else {
                        $parameters[$parameter] = $config['parameters']['static'][$parameter];
                    }
                }
            }

            if ($method === 'POST') {
                $requestData = [];
                $configMeta = $this->getConfigMeta($config, $routeData);
                if ($configMeta) {
                    $createdData = [];
                    if (array_key_exists('create', $configMeta)) {
                        foreach ($configMeta['create'] as $createKey) {
                            $createdData[$createKey] = $this->createModel($config, $createKey);
                        }
                    }

                    if (array_key_exists('model', $configMeta)) {
                        $requestData = $config['modelData'][$configMeta['model']];
                    } else {
                        $requestData = $configMeta['data'];
                        if (array_key_exists('append', $configMeta)) {
                            foreach ($configMeta['append'] as $appendKey => $appendData) {
                                $modelData = $createdData[$appendData['key']]['model'];
                                if (array_key_exists('prop', $appendData)) {
                                    $prop = $appendData['prop'];
                                    $value = $modelData->$prop;
                                } else {
                                    $value = $modelData;
                                }
                                $requestData[$appendKey] = $value;
                            }
                        }
                    }
                }
            }

            $url = route($routeName, $parameters);

            if ($routeData['aclOn']) {
                $user->roles()->sync($routeData['acl']['roles']);
                $user->privileges()->sync($routeData['acl']['privileges']);
            }

            $response = null;
            switch ($routeData['authType']) {
                case ConstantsCustom::TEST_AUTH_TYPE_SESSION:
                    $response = ($method === 'GET') ? $this->actingAs($user)->get($url) : $this->actingAs($user)->$method($url, $requestData);
                    break;
                case ConstantsCustom::TEST_AUTH_TYPE_API:
                    $response = ($method === 'GET') ? $this->get("{$url}?api_token={$user->api_token}") : $this->actingAs($user, 'api')->$method($url, $requestData);
                    break;
                case ConstantsCustom::TEST_AUTH_TYPE_NO_MATTER:
                    $response = ($method === 'GET') ? $this->get($url) : $this->$method($url, $requestData);
                    break;
                case ConstantsCustom::TEST_AUTH_TYPE_GUEST:
                    //TODO omadonex: тут может быть непонятка с auth()->logout() в случае api, но таких урлов не должно быть в принципе
                    auth()->logout();
                    $response = ($method === 'GET') ? $this->$method($url) : $this->$method($url, $requestData);
                    break;
            }

            if ($response && (($status = $response->status()) !== 200)) {
                echo "(FAILED {$status}) - '{$routeName}'";
                $failed = true;
                echo PHP_EOL;
            }
        }

        return !$failed;
    }
}
