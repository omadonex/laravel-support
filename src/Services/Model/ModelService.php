<?php
/**
 * Created by PhpStorm.
 * User: omadonex
 * Date: 06.02.2018
 * Time: 21:34
 */

namespace Omadonex\LaravelSupport\Services\Model;

use Illuminate\Database\Eloquent\Model;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxMethodNotImplementedInClassException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxModelCanNotBeEnabledException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxModelCanNotBeDisabledException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxClassNotUsesTraitException;
use Omadonex\LaravelSupport\Interfaces\Model\IModelRepository;
use Omadonex\LaravelSupport\Interfaces\Model\IModelService;
use Omadonex\LaravelSupport\Traits\CanBeEnabledTrait;

abstract class ModelService implements IModelService
{
    protected $repo;

    public function __construct(IModelRepository $repo)
    {
        $this->repo = $repo;
    }

    public function __call($name, $arguments)
    {

    }

    public function repo()
    {
        return $this->repo;
    }

    public function create($data, $fresh = true, $stopPropagation = false)
    {
        $model = $this->repo->getModel()->create($data);
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
        $className = (new \ReflectionClass($this->repo->getModel()))->getShortName() . 'Service';

        throw new OmxMethodNotImplementedInClassException($className, 'createT');
    }

    public function update($modelOrId, $data, $returnModel = false, $stopPropagation = false)
    {
        $model = $this->repo->find($modelOrId);
        $result = $model->update($data);

        if (!$stopPropagation) {
            $this->callbackUpdated($model);
        }

        return $returnModel ? $model : $result;
    }

    public function updateT($modelOrId, $data, $dataT, $returnModel = false)
    {
        $className = (new \ReflectionClass($this->repo->getModel()))->getShortName() . 'Service';

        throw new OmxMethodNotImplementedInClassException($className, 'updateT');
    }

    public function updateOrCreate($data)
    {
        $model = $this->repo->getModel()->updateOrCreate($data);
        $this->callbackUpdatedOrCreated($model);

        return $model;
    }

    public function destroy($id)
    {
        $this->repo->getModel()->destroy($id);
        $this->callbackDestroyed($id);
    }

    public function tryDestroy($id)
    {
        $this->destroy($id);
    }

    public function enable($id)
    {
        $modelClass = get_class($this->repo->getModel());
        if (!in_array(CanBeEnabledTrait::class, class_uses($modelClass))) {
            throw new OmxClassNotUsesTraitException($modelClass, CanBeEnabledTrait::class);
        }

        $model = $this->repo->find($id);
        if (!$model->canEnable()) {
            throw new OmxModelCanNotBeEnabledException($this->repo->getModel()->cantEnableText());
        }

        $model->enable();
        $this->callbackEnabled($model);
    }

    public function disable($id)
    {
        $modelClass = get_class($this->repo->getModel());
        if (!in_array(CanBeEnabledTrait::class, class_uses($modelClass))) {
            throw new OmxClassNotUsesTraitException($modelClass, CanBeEnabledTrait::class);
        }

        $model = $this->repo->find($id);
        if (!$model->canDisable()) {
            throw new OmxModelCanNotBeDisabledException($this->repo->getModel()->cantDisableText());
        }

        $model->disable();
        $this->callbackDisabled($model);
    }

    public function clear()
    {
        $this->repo->query()->delete();
    }
}