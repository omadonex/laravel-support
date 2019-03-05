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

    public function activate($activation, $data = [])
    {
        if (!$this->isActivated()) {
            $this->update(array_merge([
                'activated' => true,
            ], $data));
        }

        $activation->delete();
    }

    public function isRandom()
    {
        return $this->random;
    }
}
