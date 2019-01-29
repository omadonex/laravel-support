<?php

namespace Omadonex\LaravelSupport\Classes\Utils;

class UtilsApp
{
    const NOTIFY_ERROR = 'error';
    const NOTIFY_INFO = 'info';
    const NOTIFY_SUCCESS = 'success';
    const NOTIFY_WARNING = 'warning';

    const NOTIFY_BOTTOM_LEFT = 'bottom-left';
    const NOTIFY_BOTTOM_RIGHT = 'bottom-right';
    const NOTIFY_TOP_LEFT = 'top-left';
    const NOTIFY_TOP_RIGHT = 'top-right';

    public static function getPagesData($modules = [], $pageOrData = null)
    {
        $pages = [];
        $lang = config('app.locale');

        $pagesFilePath = resource_path("lang/{$lang}/pages.php");
        if (file_exists($pagesFilePath)) {
            $keys = array_keys(include($pagesFilePath));
            $pages['app'] = $keys;
        }

        foreach ($modules as $module) {
            $pagesFilePathPart = "Resources/lang/{$lang}/pages.php";
            $pagesFilePath = $module->getExtraPath($pagesFilePathPart);
            if (file_exists($pagesFilePath)) {
                $keys = array_keys(include($pagesFilePath));
                $pages[$module->getLowerName()] = $keys;
            }
        }

        $data = [
            'default' => [
                'breadcrumb' => trans("support::app.name"),
                'header' => trans("support::app.name"),
                'seo' => [
                    'title' => trans("support::app.name"),
                    'description' => trans("support::app.name"),
                    'keywords' => trans("support::app.name"),
                ],
            ],
        ];

        foreach ($pages as $ns => $keys) {
            foreach ($keys as $key) {
                $dataKey = $ns.ucfirst($key);
                $transNs = "pages.{$key}";
                if ($ns !== 'app') {
                    $transNs = "{$ns}::{$transNs}";
                }

                $data[$dataKey] = [
                    'breadcrumb' => trans("{$transNs}.breadcrumb"),
                    'header' => trans("{$transNs}.header"),
                    'seo' => [
                        'title' => trans("{$transNs}.seo.title"),
                        'description' => trans("{$transNs}.seo.description"),
                        'keywords' => trans("{$transNs}.seo.keywords"),
                    ],
                ];
            }
        }

        if (is_null($pageOrData)) {
            return $data;
        }

        if (!is_object($pageOrData)) {
            return array_key_exists($pageOrData, $data) ? $data[$pageOrData] : $data['default'];
        }

        return json_decode(json_encode($pageOrData->data->t->page), true);
    }

    private static function getLiveDataDefault()
    {
        return [
            'notify' => [],
            'modal' => [],
        ];
    }

    public static function addLiveNotify(
        $text,
        $type = self::NOTIFY_SUCCESS,
        $title = '',
        $isHtml = false,
        $position = self::NOTIFY_BOTTOM_RIGHT
    ) {
        $liveData = session('live', self::getLiveDataDefault());
        $liveData['notify'][] = [
            'text' => $text,
            'type' => $type,
            'title' => $title,
            'isHtml' => $isHtml,
            'positon' => $position,
        ];
        session(['live' => $liveData]);
    }

    public static function getLiveData()
    {
        $liveData = session('live', self::getLiveDataDefault());
        session()->forget('live');

        return $liveData;
    }

    public static function getAllModels($modules = [])
    {
        $getFinalModelName = function ($ns, $file) {
            $filename = $file['file'];
            $nsPart = $file['subfolder'];
            if ($nsPart) {
                $nsPart = str_replace('/', '\\', $nsPart);
                $filename = "{$nsPart}\\{$filename}";
            }

            return "{$ns}\\{$filename}";
        };

        $callback = function ($item) {
            //Обрезаем .php
            return substr(basename($item), 0, -4);
        };

        $data = [];

        $appArr = UtilsCustom::deepScandir(app_path(), false, $callback);
        foreach ($appArr as $model) {
            $filename = $model['file'];
            $data[] = "App\\{$filename}";
        }

        if (file_exists(app_path('Models'))) {
            $appModelsArr = UtilsCustom::deepScandir(app_path('Models'), true, $callback);
            foreach ($appModelsArr as $model) {
                $data[] = $getFinalModelName("App\\Models", $model);
            }
        }

        foreach ($modules as $module) {
            $moduleName = $module->getName();
            $namespace = "Modules\\$moduleName\\Models";
            $modelsPath = $module->getExtraPath("Models");
            $moduleModelsArr = UtilsCustom::deepScandir($modelsPath, true, $callback, ['.gitkeep']);
            foreach ($moduleModelsArr as $model) {
                $data[] = $getFinalModelName($namespace, $model);
            }
        }

        return $data;
    }

    public static function splitModelDataWithTranslate($data)
    {
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
}