<?php

namespace Omadonex\LaravelSupport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Omadonex\LaravelSupport\Classes\Utils\UtilsSeo;

class WebBaseController extends Controller
{
    protected $request;
    protected $isBot;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->isBot = UtilsSeo::detectBot($request->header('User-Agent'));
    }

    protected function getResourceData($resourceData, $encode = true)
    {
        $data = $resourceData->toResponse($this->request)->getData();
        return $encode ? json_encode($data) : $data;
    }
}