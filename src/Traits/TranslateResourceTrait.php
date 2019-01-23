<?php

namespace Omadonex\LaravelSupport\Traits;

trait TranslateResourceTrait
{
    public function getTranslateIfLoaded($translateResourceClass, $full = true)
    {
        $data = [];
        if ($this->resource->relationLoaded('translates')) {
            $data['t'] = new $translateResourceClass($this->getTranslate());
            if ($full) {
                $data['tHas'] = $this->hasTranslateForLang();
                $data['tList'] = $this->getAvailableLangList();
            }
        }

        return $data;
    }
}
