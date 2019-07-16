<?php

namespace Omadonex\LaravelSupport\Traits;

use Omadonex\LaravelSupport\Models\UserActivity;

trait UserActivitiesTrait
{
    public function userActivity()
    {
        return $this->hasOne(UserActivity::class);
    }
}
