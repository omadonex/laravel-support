<?php

namespace Omadonex\LaravelSupport\Traits;

trait TranslateTrait
{
    public function translates()
    {
        return $this->hasMany(get_class() . 'Translate', 'model_id');
    }

    public function getTranslate($lang = null, $defaultLangProp = null)
    {
        $langKey = $lang ?: app('locale')->getCurrLanguage();
        $filtered = $this->translates->filter(function ($value, $key) use ($langKey) {
            return $value->lang === $langKey;
        });

        if ($filtered->count()) {
            return $filtered->first();
        }

        $defaultLangKey = app('locale')->getDefaultLanguage();
        if ($defaultLangProp && property_exists($this, $defaultLangProp)) {
            $defaultLangKey = $defaultLangProp;
        }

        //TODO omadonex: с этой строчкой работает drawfox
        //$defaultLangKey === $this->lang_original;

        return $this->translates->filter(function ($value, $key) use ($defaultLangKey) {
            return $value->lang === $defaultLangKey;
        })->first();
    }

    public function hasTranslateForLang($lang = null)
    {
        $langKey = $lang ?: app('locale')->getCurrLanguage();

        return in_array($langKey, $this->translates->pluck('lang')->all());
    }

    public function getAvailableLangList()
    {
        return app('locale')->getLanguageList($this->translates->pluck('lang')->all());
    }
}
