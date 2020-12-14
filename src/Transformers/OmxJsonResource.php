<?php

namespace Omadonex\LaravelSupport\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class OmxJsonResource extends JsonResource
{
    protected $params;

    public function __construct($resource, $params = [])
    {
        parent::__construct($resource);

        $this->params = $params;
    }
}
