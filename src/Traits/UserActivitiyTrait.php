<?php

namespace Omadonex\LaravelSupport\Traits;

use Omadonex\LaravelSupport\Models\UserActivity;

trait UserActivitiyTrait
{
    public function activity()
    {
        return $this->hasOne(UserActivity::class);
    }
}
