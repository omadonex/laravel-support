<?php

namespace Omadonex\LaravelSupport\Interfaces\Model;

interface IModelLog
{
    /**
     * Сохранение в лог
     * @param $model
     * @return mixed
     */
    public function saveToLog($model);
}