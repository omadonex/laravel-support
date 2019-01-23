<?php

namespace Omadonex\LaravelSupport\Traits;

use Omadonex\LaravelSupport\Models\UserActivation;

trait UserActivationTrait
{
    public function userActivation()
    {
        return $this->hasOne(UserActivation::class);
    }

    public function isActivated()
    {
        return $this->activated;
    }

    public function activate($data)
    {
        $this->update([
            'activated' => true,
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
