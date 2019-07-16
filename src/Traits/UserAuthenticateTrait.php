<?php

namespace Omadonex\LaravelSupport\Traits;

use Omadonex\LaravelSupport\Models\UserAuthenticate;

trait UserAuthenticateTrait
{
    public function authenticates()
    {
        return $this->hasMany(UserAuthenticate::class);
    }
}
