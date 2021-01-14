<?php
/**
 * Created by PhpStorm.
 * User: omadonex
 * Date: 06.02.2018
 * Time: 21:34
 */

namespace Omadonex\LaravelSupport\Services\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Omadonex\LaravelSupport\Classes\ConstantsCustom;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxClassNotUsesTraitException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxMethodNotImplementedInClassException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxModelCanNotBeDisabledException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxModelCanNotBeEnabledException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxModelNotSearchedException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxModelNotSmartFoundException;
use Omadonex\LaravelSupport\Interfaces\Model\IModelRepository;
use Omadonex\LaravelSupport\Traits\CanBeEnabledTrait;
use Omadonex\LaravelSupport\Transformers\PaginateResourceCollection;

abstract class ModelRepository implements IModelRepository
{
    protected $model;
    protected $modelClass;
    protected $resourceClass;

    public function __construct(Model $model, $resourceClass)
    {
        $this->model = $model;
        $this->modelClass = get_class($model);
        $this->resourceClass = $resourceClass;
    }

    private function getRealOptions($options)
    {
        $keysValues = [
            'exceptions' => false,
            'resource' => false,
            'resourceClass' => null,
            'resourceParams' => [],
            'relations' => false,
            'trashed' => null,
            'smart' => false,
            'smartField' => null,
            'enabled' => null,
            'paginate' => false,
            'closures' => [],
        ];

        $realOptions = [];
        foreach ($keysValues as $key => $value) {
            $realOptions[$key] = array_key_exists($key, $options) ? $options[$key] : $value;
        }

        return $realOptions;
    }

    protected function attachRelations($qb, $options)
    {
        $prop = 'availableRelations';
        if (($options['relations'] === true)
            && property_exists($this->modelClass, $prop)
            && is_array($this->model->$prop)) {
            $qb->with($this->model->$prop);
        }

        if (is_array($options['relations'])) {
            $qb->with($options['relations']);
        }

        return $qb;
    }

    protected function getPaginatedResult($qb, $paginate)
    {
        if (!$paginate) {
            return $qb->get();
        }

        return $qb->paginate(($paginate === true) ? $this->model->getPerPage() : $paginate);
    }

    /**
     * @param $options
     * @return mixed
     * @throws OmxClassNotUsesTraitException
     */
    protected function makeQB($options)
    {
        $qb = $this->model->query();

        if (!is_null($options['trashed'])) {
            if (!in_array(SoftDeletes::class, class_uses($this->modelClass))) {
                throw new OmxClassNotUsesTraitException($this->modelClass, SoftDeletes::class);
            }

            if ($options['trashed'] === ConstantsCustom::DB_QUERY_TRASHED_WITH) {
                $qb->withTrashed();
            }

            if ($options['trashed'] === ConstantsCustom::DB_QUERY_TRASHED_ONLY) {
                $qb->onlyTrashed();
            }
        }

        if (!is_null($options['enabled'])) {
            if (!in_array(CanBeEnabledTrait::class, class_uses($this->modelClass))) {
                throw new OmxClassNotUsesTraitException($this->modelClass, CanBeEnabledTrait::class);
            }
            $qb->byEnabled($options['enabled']);
        }

        foreach ($options['closures'] as $closure) {
            if (is_callable($closure)) {
                $qb = $closure($qb);
            }
        }

        return $this->attachRelations($qb, $options);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function query()
    {
        return $this->model->query();
    }

    public function getAvailableRelations()
    {
        return $this->model->availableRelations ?: [];
    }

    public function toResource($modelOrCollection, $resource = false, $resourceClass = null, $resourceParams = [], $paginate = false)
    {
        if (!$resource) {
            return $modelOrCollection;
        }

        $finalResourceClass = $resourceClass ?: $this->resourceClass;

        if ($modelOrCollection instanceof Model) {
            if ($resourceParams) {
                return new $finalResourceClass($modelOrCollection, $resourceParams);
            }

            return new $finalResourceClass($modelOrCollection);
        }

        if ($paginate) {
            return new PaginateResourceCollection($modelOrCollection, $finalResourceClass);
        }

        return $finalResourceClass::collection($modelOrCollection);
    }

    /**
     * @param $id
     * @param $options
     * @return mixed|null|PaginateResourceCollection
     * @throws OmxClassNotUsesTraitException
     * @throws OmxModelNotSmartFoundException
     */
    private function doFind($id, $options)
    {
        $realOptions = $this->getRealOptions($options);

        $field = $this->model->getKeyName();
        if ($realOptions['smart']) {
            $field = $realOptions['smartField'] ?: $this->model->getRouteKeyName();
        }
        $model = $this->makeQB($realOptions)->where($field, $id)->first();
        if (is_null($model)) {
            if ($realOptions['exceptions']) {
                throw new OmxModelNotSmartFoundException($this->model, $id, $field);
            }

            return null;
        }

        return $this->toResource($model, $realOptions['resource'], $realOptions['resourceClass'], $realOptions['resourceParams'], false);
    }

    public function find($modelOrId, $options = [])
    {
        if ($modelOrId instanceof Model) {
            return $modelOrId;
        }

        return $this->doFind($modelOrId, $options);
    }

    public function search($options = [])
    {
        $realOptions = $this->getRealOptions($options);
        $model = $this->makeQB($realOptions)->first();
        if (is_null($model)) {
            if ($realOptions['exceptions']) {
                throw new OmxModelNotSearchedException($this->model);
            }

            return null;
        }

        return $this->toResource($model, $realOptions['resource'], $realOptions['resourceClass'], $realOptions['resourceParams'], false);
    }

    public function list($options = [])
    {
        $realOptions = $this->getRealOptions($options);

        $collection = $this->getPaginatedResult($this->makeQB($realOptions), $realOptions['paginate']);

        return $this->toResource($collection, $realOptions['resource'], $realOptions['resourceClass'], $realOptions['resourceParams'], $realOptions['paginate']);
    }

    public function agrCount($options = [])
    {
        $realOptions = $this->getRealOptions($options);

        return $this->makeQB($realOptions)->count();
    }

    public function create($data, $fresh = true, $stopPropagation = false)
    {
        $model = $this->getModel()->create($data);
        if ($fresh) {
            $model = $model->fresh();
        }

        if (!$stopPropagation) {
            $this->callbackCreated($model);
        }

        return $model;
    }

    public function createT($data, $dataT, $fresh = true)
    {
        $className = (new \ReflectionClass($this->getModel()))->getShortName() . 'Service';

        throw new OmxMethodNotImplementedInClassException($className, 'createT');
    }

    public function update($modelOrId, $data, $returnModel = false, $stopPropagation = false)
    {
        $model = $this->find($modelOrId);
        $result = $model->update($data);

        if (!$stopPropagation) {
            $this->callbackUpdated($model);
        }

        return $returnModel ? $model : $result;
    }

    public function updateT($modelOrId, $data, $dataT, $returnModel = false)
    {
        $className = (new \ReflectionClass($this->getModel()))->getShortName() . 'Service';

        throw new OmxMethodNotImplementedInClassException($className, 'updateT');
    }

    public function updateOrCreate($data)
    {
        $model = $this->getModel()->updateOrCreate($data);
        $this->callbackUpdatedOrCreated($model);

        return $model;
    }

    public function destroy($id)
    {
        $this->getModel()->destroy($id);
        $this->callbackDestroyed($id);
    }

    public function tryDestroy($id)
    {
        $this->destroy($id);
    }

    public function enable($id)
    {
        $modelClass = get_class($this->getModel());
        if (!in_array(CanBeEnabledTrait::class, class_uses($modelClass))) {
            throw new OmxClassNotUsesTraitException($modelClass, CanBeEnabledTrait::class);
        }

        $model = $this->find($id);
        if (!$model->canEnable()) {
            throw new OmxModelCanNotBeEnabledException($this->getModel()->cantEnableText());
        }

        $model->enable();
        $this->callbackEnabled($model);
    }

    public function disable($id)
    {
        $modelClass = get_class($this->getModel());
        if (!in_array(CanBeEnabledTrait::class, class_uses($modelClass))) {
            throw new OmxClassNotUsesTraitException($modelClass, CanBeEnabledTrait::class);
        }

        $model = $this->find($id);
        if (!$model->canDisable()) {
            throw new OmxModelCanNotBeDisabledException($this->getModel()->cantDisableText());
        }

        $model->disable();
        $this->callbackDisabled($model);
    }

    public function clear($force = false)
    {
        if ($force) {
            $this->query()->forceDelete();
        } else {
            $this->query()->delete();
        }
    }
}