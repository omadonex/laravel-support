<?php

namespace Omadonex\LaravelSupport\Interfaces\Model;

interface IModelService
{
    /**
     * Возвращает ModelRepository
     * @return mixed
     */
    public function repo();

    /**
     * Создает новую модель по введенным данным и возращает ее
     * @param $data
     * @param bool $fresh
     * @param bool $stopPropagation
     * @return mixed
     */
    public function create($data, $fresh = true, $stopPropagation = false);

    /**
     * Создает новую модель вместе со связанными моделями переводов
     * Этот метод необходимо переопределить в модели, в которой он понадобится, так как базовая реализация
     * генерирует исключение
     * @param $data
     * @param $dataT
     * @param bool $fresh
     * @return mixed
     */
    public function createT($data, $dataT, $fresh = true);

    /**
     * Обновляет поля модели и возвращает обновленную модель
     * @param $modelOrId
     * @param $data
     * @param bool $returnModel
     * @param bool $stopPropagation
     * @return mixed
     */
    public function update($modelOrId, $data, $returnModel = false, $stopPropagation = false);

    /**
     * Обновляет поля модели вместе со связанными моделями переводов
     * Этот метод необходимо переопределить в модели, в которой он понадобится, так как базовая реализация
     * генерирует исключение
     * @param $modelOrId
     * @param $data
     * @param $dataT
     * @param bool $returnModel
     * @return mixed
     */
    public function updateT($modelOrId, $data, $dataT, $returnModel = false);

    /**
     * Обновляет существущую либо создает новую модель
     * @param $data
     * @return mixed
     */
    public function updateOrCreate($data);

    /**
     * Удаляет модель
     * @param $id
     * @return mixed
     */
    public function destroy($id);

    /**
     * Выполняет попытку удаления модели, необходимо переопределять
     * @param $id
     * @return mixed
     */
    public function tryDestroy($id);

    /**
     * Включение
     * @param $id
     * @return mixed
     */
    public function enable($id);

    /**
     * Отключение
     * @param $id
     * @return mixed
     */
    public function disable($id);

    /**
     * Удаляет все записи в таблице
     * @param $force
     * @return mixed
     */
    public function clear($force = false);
}