<?php

namespace Omadonex\LaravelSupport\Traits\Controllers;

trait CanBeEnabledControllerTrait
{
    public function enable($modelId)
    {
        $this->service->enable($modelId);

        return $this->okResponse();
    }

    public function disable($modelId)
    {
        $this->service->disable($modelId);

        return $this->okResponse();
    }
}
