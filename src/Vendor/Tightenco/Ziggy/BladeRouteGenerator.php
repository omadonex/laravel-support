<?php

namespace Omadonex\LaravelSupport\Vendor\Tigtenco\Ziggy;

use Illuminate\Routing\Router;
use Tightenco\Ziggy\RoutePayload;

class BladeRouteGenerator
{
    private $baseDomain;
    private $basePort;
    private $baseUrl;
    private $baseProtocol;
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getRoutePayload($group = false)
    {
        return RoutePayload::compile($this->router, $group);
    }

    public function generate($group = false)
    {
        $this->prepareDomain();
        $defaultParameters = method_exists(app('url'), 'getDefaultParameters') ? json_encode(app('url')->getDefaultParameters()) : '[]';
        
        return [
            'namedRoutes' => $this->getRoutePayload($group),
            'baseUrl' => $this->baseUrl,
            'baseProtocol' => $this->baseProtocol,
            'baseDomain' => $this->baseDomain,
            'basePort' => $this->basePort,
            'defaultParameters' => $defaultParameters,
        ];
    }

    private function prepareDomain()
    {
        $url = url('/');
        $parsedUrl = parse_url($url);

        $this->baseUrl = $url . '/';
        $this->baseProtocol = array_key_exists('scheme', $parsedUrl) ? $parsedUrl['scheme'] : 'http';
        $this->baseDomain = array_key_exists('host', $parsedUrl) ? $parsedUrl['host'] : '';
        $this->basePort = array_key_exists('port', $parsedUrl) ? $parsedUrl['port'] : 'false';
    }
}
