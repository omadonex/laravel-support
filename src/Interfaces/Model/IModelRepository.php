<?php

namespace Omadonex\LaravelSupport\Interfaces\Model;

use Omadonex\LaravelSupport\Classes\Exceptions\OmxClassNotUsesTraitException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxModelNotSearchedException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxModelNotSmartFoundException;

interface IModelRepository
{
    /**
     * Возвращает класс модели
     * @return mixed
     */
    public function getModel();

    /**
     * Возвращает Query builder для построения запросов
     * @return mixed
     */
    public function query();

    /**
     * Возвращает массив доступных связей модели, либо пустой массив, если свойство отсутствует
     * @return array
     */
    public function getAvailableRelations();

    /**
     * Приводит результат запроса в состояние ресурса
     *
     * @param $modelOrCollection
     * @param false $resource
     * @param null $resourceClass
     * @param array $resourceParams
     * @param false $paginate
     * @return mixed
     */
    public function toResource($modelOrCollection, $resource = false, $resourceClass = null, $resourceParams = [], $paginate = false);

    /**
     * @param $modelOrId
     * @param array $options
     *
     * 'exceptions' => false | true
     * 'resource' => false | true
     * 'resourceClass' => null | class
     * 'relations' => false | true | array
     * 'trashed' => null | with | only
     * 'smart' => false | true
     * 'smartField' => null | string
     * 'enabled' => null | false | true
     * 'closures' => []
     *
     * @throws OmxModelNotSmartFoundException
     * @throws OmxClassNotUsesTraitException
     * @return mixed
     */
    public function find($modelOrId, $options = []);

    /**
     * Выполняет поиск по заданным критериям, может генерировать исключение
     * @param array $options
     *
     * 'exceptions' => false | true
     * 'resource' => false | true
     * 'resourceClass' => null | class
     * 'relations' => false | true | array
     * 'trashed' => null | with | only
     * 'enabled' => null | false | true
     * 'closures' => []
     *
     * @throws OmxModelNotSearchedException
     * @throws OmxClassNotUsesTraitException
     * @return mixed
     */
    public function search($options = []);

    /**
     * Получает коллекцию элементов, загружая указанные связи и учитывая `enabled`
     * Возвращает пагинатор либо коллекцию, если кол-во элементов не указано, то оно будет взято из модели
     * @param array $options
     *
     * 'resource' => false | true
     * 'resourceClass' => null | class
     * 'relations' => false | true | array
     * 'trashed' => null | with | only
     * 'enabled' => null | false | true
     * 'paginate' => false | true | integer
     * 'closures' => []
     *
     * @throws OmxClassNotUsesTraitException
     * @return mixed
     */
    public function list($options = []);

    /**
     * Агрегатная функция подсчета количества элементов
     * @param array $options
     *
     * 'trashed' => null | with | only
     * 'enabled' => null | false | true
     * 'closures' => []
     *
     * @throws OmxClassNotUsesTraitException
     * @return mixed
     */
    public function agrCount($options = []);
}