<?php

namespace Omadonex\LaravelSupport\Traits;

trait PersonNamesTrait
{
    protected function getDefaultNameAttribute()
    {
        return trans('support::common.user');
    }

    /**
     * Полное обращение к человеку (Имя Фамилия и Отчество)
     * @return string
     */
    public function getFullNameAttribute()
    {
        $str = trim($this->last_name . ' ' . $this->first_name . ' ' . $this->opt_name);

        return $str ?: $this->getDefaultNameAttribute();
    }

    /**
     * Короткое обращение к человеку (Имя и Фамилия)
     * @return string
     */
    public function getShortNameAttribute()
    {
        $str = trim($this->first_name . ' ' . $this->last_name);

        return $str ?: $this->getDefaultNameAttribute();
    }

    /**
     * Официальное обращение к человеку (Имя и Отчество)
     * @return string
     */
    public function getOfficialNameAttribute()
    {
        $str = trim($this->first_name . ' ' . $this->opt_name);

        return $str ?: $this->getDefaultNameAttribute();
    }

    /**
     * Фамилия, инициалы
     * @return string
     */
    public function getInitialsNameAttribute()
    {
        $initials = '';
        if ($this->first_name) {
            $initials .= mb_substr($this->first_name, 0, 1) . '.';
        }
        if ($this->opt_name) {
            $initials .= mb_substr($this->opt_name, 0, 1) . '.';
        }
        $str = trim($this->last_name . ' ' . $initials);

        return $str ?: $this->getDefaultNameAttribute();
    }
}
