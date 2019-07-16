<?php

namespace Omadonex\LaravelSupport\Traits;

use Omadonex\LaravelSupport\Models\UserMeta;

trait UserMetaTrait
{
    public function meta()
    {
        return $this->hasOne(UserMeta::class);
    }
}
