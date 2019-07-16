<?php

namespace Omadonex\LaravelSupport\Traits;

use Omadonex\LaravelSupport\Models\UserActivity;

trait UserActivitiesTrait
{
    public function activity()
    {
        return $this->hasOne(UserActivity::class);
    }
}
