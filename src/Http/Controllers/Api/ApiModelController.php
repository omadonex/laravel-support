<?php

namespace Omadonex\LaravelSupport\Http\Controllers\Api;

use Illuminate\Http\Request;
use Omadonex\LaravelSupport\Classes\ConstantsCustom;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterEnabledException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterPaginateException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterRelationsException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterTrashedException;
use Omadonex\LaravelSupport\Classes\Utils\UtilsApp;
use Omadonex\LaravelSupport\Interfaces\Model\IModelRepository;
use Omadonex\LaravelSupport\Interfaces\Model\IModelService;

class ApiModelController extends ApiBaseController
{
    protected $repo;
    protected $service;

    protected $trashed;
    protected $relations;
    protected $enabled;
    protected $paginate;

    public function __construct(IModelRepository $repo, IModelService $service, Request $request)
    {
        parent::__construct($request);
        $this->repo = $repo;
        $this->service = $service;

        $this->relations = $this->getParamRelations($request, $this->repo->getAvailableRelations());

        if ($request->isMethod('get')) {
            $this->trashed = $this->getParamTrashed($request);
            $this->enabled = $this->getParamEnabled($request);
            $this->paginate = $this->getParamPaginate($request);
        }
    }

    private function getParamRelations(Request $request, $availableRelations)
    {
        $key = ConstantsCustom::REQUEST_PARAM_RELATIONS;
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
        $key = ConstantsCustom::REQUEST_PARAM_ENABLED;
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
        $key = ConstantsCustom::REQUEST_PARAM_PAGINATE;
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
        $key = ConstantsCustom::REQUEST_PARAM_TRASHED;
        $data = $request->all();
        if (!array_key_exists($key, $data)) {
            return null;
        }

        if (in_array($data[$key], [ConstantsCustom::DB_QUERY_TRASHED_WITH, ConstantsCustom::DB_QUERY_TRASHED_ONLY])) {
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
        return $this->repo->find($id, [
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
        return $this->repo->search([
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

        return ($method === 'list') ? $this->repo->list($options) : $this->repo->$method($methodParams, $options);
    }

    protected function modelCreate($data, $resource = false, $resourceClass = null, $resourceParams = [])
    {
        $model = $this->service->create($data);
        $model->load($this->getRelations($model));

        return $this->repo->toResource($model, $resource, $resourceClass, $resourceParams, false);
    }

    protected function modelCreateT($data, $resource = false, $resourceClass = null, $resourceParams = [])
    {
        $dataSplit = UtilsApp::splitModelDataWithTranslate($data);

        $model = $this->service->createT($dataSplit['data'], $dataSplit['dataT']);
        $model->load($this->getRelations($model));

        return $this->repo->toResource($model, $resource, $resourceClass, $resourceParams, false);
    }

    protected function modelUpdate($id, $data, $resource = false, $resourceClass = null, $resourceParams = [])
    {
        $model = $this->service->update($id, $data, true);
        $model->load($this->getRelations($model));

        return $this->repo->toResource($model, $resource, $resourceClass, $resourceParams, false);
    }

    protected function modelUpdateT($id, $data, $resource = false, $resourceClass = null, $resourceParams = [])
    {
        $dataSplit = UtilsApp::splitModelDataWithTranslate($data);
        $model = $this->service->updateT($id, $dataSplit['data'], $dataSplit['dataT'], true);
        $model->load($this->getRelations($model));

        return $this->repo->toResource($model, $resource, $resourceClass, $resourceParams,  false);
    }
}