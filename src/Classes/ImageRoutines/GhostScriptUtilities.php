<?php

namespace Omadonex\LaravelSupport\Classes\ImageRoutines;

use Illuminate\Support\Facades\Storage;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class GhostScriptUtilities
{
    /**
     * @return string
     */
    private static function getTempFolder()
    {
        $str = UtilsCustom::random_str(20);
        $folder = "temp/{$str}";
        Storage::disk('local')->makeDirectory($folder);

        return $folder;
    }

    /**
     * @param $contents
     * @param null $resolution
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function makePreview($contents, $resolution = null)
    {
        $folder = self::getTempFolder();
        $inputPath = storage_path("app/{$folder}/input");
        $outputPath = storage_path("app/{$folder}/output");
        Storage::disk('local')->put("{$folder}/input", $contents);

        GhostScriptProcessor::makePreview($inputPath, $outputPath, $resolution);
        $resultContents = Storage::disk('local')->get("{$folder}/output");
        Storage::disk('local')->deleteDirectory($folder);

        return $resultContents;
    }

    /**
     * @param $path
     * @param null $resolution
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function makePreviewFromFile($path, $resolution = null)
    {
        $folder = self::getTempFolder();
        $outputPath = storage_path("app/{$folder}/output");
        GhostScriptProcessor::makePreview($path, $outputPath, $resolution);
        $resultContents = Storage::disk('local')->get("{$folder}/output");
        Storage::disk('local')->deleteDirectory($folder);

        return $resultContents;
    }
}
