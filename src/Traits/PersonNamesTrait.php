<?php

namespace Omadonex\LaravelSupport\Traits;

use Ramsey\Uuid\Uuid;

trait PersonNamesTrait
{
    /**
     * Имя по умолчанию (если ничего больше не указано)
     * @return string
     */
    protected function getDefaultNameAttribute(): string
    {
        return trans('support::common.user');
    }

    /**
     * Имя пользователя (usernam)
     * @param string $value
     * @return string
     */
    public function getUsernameAttribute(string $value): string
    {
        if (Uuid::isValid($value)) {
            return $this->defaultName;
        }

        return $value;
    }

    /**
     * Отображаемое имя (как указал пользователь)
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('meta') && $this->meta->display_name) {
            return $this->meta->display_name;
        }

        return $this->username;
    }

    /**
     * Полное обращение к человеку (Фамилия Имя Отчество)
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        $str = trim($this->last_name . ' ' . $this->first_name . ' ' . $this->opt_name);

        return $str ?: $this->username;
    }

    /**
     * Короткое обращение к человеку (Имя и Фамилия)
     * @return string
     */
    public function getShortNameAttribute(): string
    {
        $str = trim($this->first_name . ' ' . $this->last_name);

        return $str ?: $this->username;
    }

    /**
     * Официальное обращение к человеку (Имя и Отчество)
     * @return string
     */
    public function getOfficialNameAttribute(): string
    {
        $str = trim($this->first_name . ' ' . $this->opt_name);

        return $str ?: $this->username;
    }

    /**
     * Фамилия, инициалы
     * @return string
     */
    public function getInitialsNameAttribute(): string
    {
        $initials = '';
        if ($this->first_name) {
            $initials .= mb_substr($this->first_name, 0, 1) . '.';
        }
        if ($this->opt_name) {
            $initials .= mb_substr($this->opt_name, 0, 1) . '.';
        }
        $str = trim($this->last_name . ' ' . $initials);

        return $str ?: $this->username;
    }
}
