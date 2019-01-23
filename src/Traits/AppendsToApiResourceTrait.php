<?php

namespace Omadonex\LaravelSupport\Traits;

trait AppendsToApiResourceTrait
{
    public function with($request)
    {
        $data = $request->all();
        unset($data['page']);

        return [
            'appends' => $data,
        ];
    }
}
