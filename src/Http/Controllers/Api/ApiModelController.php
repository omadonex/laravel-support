<?php

namespace Omadonex\LaravelSupport\Http\Controllers\Api;

use Illuminate\Http\Request;
use Omadonex\LaravelSupport\Classes\ConstantsCustom;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterActiveException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterPaginateException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterRelationsException;
use Omadonex\LaravelSupport\Classes\Exceptions\OmxBadParameterTrashedException;
use Omadonex\LaravelSupport\Interfaces\Model\IModelRepository;
use Omadonex\LaravelSupport\Interfaces\Model\IModelService;

class ApiModelController extends ApiBaseController
{
    protected $repo;
    protected $service;

    protected $trashed;
    protected $relations;
    protected $active;
    protected $paginate;

    public function __construct(IModelRepository $repo, IModelService $service, Request $request)
    {
        parent::__construct($request);
        $this->repo = $repo;
        $this->service = $service;

        $this->relations = $this->getParamRelations($request, $this->repo->getAvailableRelations());

        if ($request->isMethod('get')) {
            $this->trashed = $this->getParamTrashed($request);
            $this->active = $this->getParamActive($request);
            $this->paginate = $this->getParamPaginate($request);
        }
    }

    private function getParamRelations(Request $request, $availableRelations)
    {
        $key = $request->isMethod('get') ? 'relations' : '__relations';
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

    private function getParamActive(Request $request)
    {
        $data = $request->all();
        if (!array_key_exists('active', $data)) {
            return null;
        }

        if ($data['active'] === 'true') {
            return true;
        }

        if ($data['active'] === 'false') {
            return false;
        }

        throw new OmxBadParameterActiveException;
    }

    private function getParamPaginate(Request $request)
    {
        $data = $request->all();
        if (!array_key_exists('paginate', $data) || ($data['paginate'] === 'true')) {
            return true;
        }

        if ($data['paginate'] === 'false') {
            return false;
        }

        if (is_numeric($data['paginate'])) {
            return $data['paginate'];
        }

        throw new OmxBadParameterPaginateException;
    }

    private function getParamTrashed(Request $request)
    {
        $data = $request->all();
        if (!array_key_exists('trashed', $data)) {
            return null;
        }

        if (in_array($data['trashed'], [ConstantsCustom::DB_QUERY_TRASHED_WITH, ConstantsCustom::DB_QUERY_TRASHED_ONLY])) {
            return $data['trashed'];
        }

        throw new OmxBadParameterTrashedException;
    }

    private function splitData($data) {
        $dataM = [];

        foreach ($data as $key => $value) {
            if ((substr($key, 0, 2) !== '__') && ($key !== 't')) {
                $dataM[$key] = $value;
            }
        }

        return [
            'data' => $dataM,
            'dataT' => $data['t'],
        ];
    }

    protected function modelFind($id, $resource = false, $resourceClass = null, $smart = false, $smartField = null, $closures = [])
    {
        return $this->repo->find($id, [
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'relations' => $this->relations,
            'trashed' => $this->trashed,
            'active' => $this->active,
            'smart' => $smart,
            'smartField' => $smartField,
            'closures' => $closures,
        ]);
    }

    protected function modelSearch($resource = false, $resourceClass = null, $closures = [])
    {
        return $this->repo->search([
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'relations' => $this->relations,
            'trashed' => $this->trashed,
            'active' => $this->active,
            'closures' => $closures,
        ]);
    }

    protected function modelList($resource = false, $resourceClass = null, $methodName = null, $methodParams = [], $closures = [])
    {
        $options = [
            'resource' => $resource,
            'resourceClass' => $resourceClass,
            'relations' => $this->relations,
            'trashed' => $this->trashed,
            'active' => $this->active,
            'paginate' => $this->paginate,
            'closures' => $closures,
        ];
        $method = $methodName ?: 'list';

        return ($method === 'list') ? $this->repo->list($options) : $this->repo->$method($methodParams, $options);
    }

    protected function modelCreate($data, $resource = false, $resourceClass = null)
    {
        $model = $this->service->create($data);
        $model->load($this->relations);

        return $this->repo->toResource($model, $resource, $resourceClass, false);
    }

    protected function modelCreateT($data, $resource = false, $resourceClass = null)
    {
        $dataSplit = $this->splitData($data);

        $model = $this->service->createT($dataSplit['data'], $dataSplit['dataT']);
        $model->load($this->relations);

        return $this->repo->toResource($model, $resource, $resourceClass, false);
    }

    protected function modelUpdate($id, $data, $resource = false, $resourceClass = null)
    {
        $model = $this->service->update($id, $data, true);
        $model->load($this->relations);

        return $this->repo->toResource($model, $resource, $resourceClass, false);
    }

    protected function modelUpdateT($id, $data, $resource = false, $resourceClass = null)
    {
        $dataSplit = $this->splitData($data);

        $model = $this->service->updateT($id, $dataSplit['data'], $dataSplit['dataT'], true);
        $model->load($this->relations);

        return $this->repo->toResource($model, $resource, $resourceClass, false);
    }
}