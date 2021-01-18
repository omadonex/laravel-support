<?php

namespace Omadonex\LaravelSupport\Http\Controllers\Api;

use Illuminate\Http\Request;
use Omadonex\LaravelSupport\Classes\ConstCustom;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterEnabledException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterPaginateException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterRelationsException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterTrashedException;
use Omadonex\LaravelSupport\Classes\Utils\UtilsApp;
use Omadonex\LaravelSupport\Interfaces\Model\IModelRepository;

class ApiModelController extends ApiBaseController
{
    /** @var IModelRepository */
    protected $repository;

    protected $trashed;
    protected $relations;
    protected $enabled;
    protected $paginate;

    public function __construct(IModelRepository $repository, Request $request)
    {
        parent::__construct($request);
        $this->repository = $repository;

        $this->relations = $this->getParamRelations($request, $this->repository->getAvailableRelations());

        if ($request->isMethod('get')) {
            $this->trashed = $this->getParamTrashed($request);
            $this->enabled = $this->getParamEnabled($request);
            $this->paginate = $this->getParamPaginate($request);
        }
    }

    private function getParamRelations(Request $request, $availableRelations)
    {
        $key = ConstCustom::REQUEST_PARAM_RELATIONS;
        $data = $request->all();
        if (!array_key_exists($key, $data) || ($data[$key] === 'false')) {
            return false;
        }

        if ($data[$key] === 'true') {
            return true;
        }

        if (is_array($data[$key])) {
            $relations = [];
            foreach ($data[$key] as $relation) {
                $insertRelation = $relation;
                if (strpos($relation, '.') !== false) {
                    $insertRelation = explode('.', $relation)[0];
                }
                $relations[] = $insertRelation;
            }
            if (empty(array_diff($relations, $availableRelations))) {
                return $data[$key];
            }
        }

        throw new OmxBadParameterRelationsException($availableRelations);
    }

    private function getParamEnabled(Request $request)
    {
        $key = ConstCustom::REQUEST_PARAM_ENABLED;
        $data = $request->all();
        if (!array_key_exists($key, $data)) {
            return null;
        }

        if ($data[$key] === 'true') {
            return true;
        }

        if ($data[$key] === 'false') {
            return false;
        }

        throw new OmxBadParameterEnabledException;
    }

    private function getParamPaginate(Request $request)
    {
        $key = ConstCustom::REQUEST_PARAM_PAGINATE;
        $data = $request->all();
        if (!array_key_exists($key, $data) || ($data[$key] === 'true')) {
            return true;
        }

        if ($data[$key] === 'false') {
            return false;
        }

        if (is_numeric($data[$key])) {
            return $data[$key];
        }

        throw new OmxBadParameterPaginateException;
    }

    private function getParamTrashed(Request $request)
    {
        $key = ConstCustom::REQUEST_PARAM_TRASHED;
        $data = $request->all();
        if (!array_key_exists($key, $data)) {
            return null;
        }

        if (in_array($data[$key], [ConstCustom::DB_QUERY_TRASHED_WITH, ConstCustom::DB_QUERY_TRASHED_ONLY])) {
            return $data[$key];
        }

        throw new OmxBadParameterTrashedException;
    }

    private function getRelations($model)
    {
        $prop = 'availableRelations';
        if (($this->relations === true)
            && property_exists(get_class($model), $prop)
            && is_array($model->$prop)) {
            return $model->$prop;
        }

        if (is_array($this->relations)) {
            return $this->relations;
        }

        return [];
    }

    protected function modelFind($id, $resource = false, $resourceClass = null, $resourceParams = [], $smart = false, $smartField = null, $closures = [])
    {
        return $this->repository->find($id, [
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'resourceParams' => $resourceParams,
            'enabled' => $this->enabled,
            'relations' => $this->relations,
            'trashed' => $this->trashed,
            'smart' => $smart,
            'smartField' => $smartField,
            'closures' => $closures,
        ]);
    }

    protected function modelSearch($resource = false, $resourceClass = null, $resourceParams = [], $closures = [])
    {
        return $this->repository->search([
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'resourceParams' => $resourceParams,
            'enabled' => $this->enabled,
            'relations' => $this->relations,
            'trashed' => $this->trashed,
            'closures' => $closures,
        ]);
    }

    protected function modelList($resource = false, $resourceClass = null, $resourceParams = [], $methodName = null, $methodParams = [], $closures = [])
    {
        $options = [
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'resourceParams' => $resourceParams,
            'enabled' => $this->enabled,
            'paginate' => $this->paginate,
            'relations' => $this->relations,
            'trashed' => $this->trashed,
            'closures' => $closures,
        ];
        $method = $methodName ?: 'list';

        return ($method === 'list') ? $this->repository->list($options) : $this->repository->$method($methodParams, $options);
    }

    protected function modelCreate($data, $resource = false, $resourceClass = null, $resourceParams = [])
    {
        $model = $this->repository->create($data);
        $model->load($this->getRelations($model));

        return $this->repository->toResource($model, $resource, $resourceClass, $resourceParams, false);
    }

    protected function modelCreateT($data, $resource = false, $resourceClass = null, $resourceParams = [])
    {
        $dataSplit = UtilsApp::splitModelDataWithTranslate($data);

        $model = $this->repository->createT($dataSplit['data'], $dataSplit['dataT']);
        $model->load($this->getRelations($model));

        return $this->repository->toResource($model, $resource, $resourceClass, $resourceParams, false);
    }

    protected function modelUpdate($id, $data, $resource = false, $resourceClass = null, $resourceParams = [])
    {
        $model = $this->repository->update($id, $data, true);
        $model->load($this->getRelations($model));

        return $this->repository->toResource($model, $resource, $resourceClass, $resourceParams, false);
    }

    protected function modelUpdateT($id, $data, $resource = false, $resourceClass = null, $resourceParams = [])
    {
        $dataSplit = UtilsApp::splitModelDataWithTranslate($data);
        $model = $this->repository->updateT($id, $dataSplit['data'], $dataSplit['dataT'], true);
        $model->load($this->getRelations($model));

        return $this->repository->toResource($model, $resource, $resourceClass, $resourceParams,  false);
    }
}