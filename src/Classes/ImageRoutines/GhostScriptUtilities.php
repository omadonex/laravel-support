<?php

namespace Omadonex\LaravelSupport\Classes\ImageRoutines;

use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class GhostScriptUtilities
{
    private static function getTempFolder()
    {
        $str = UtilsCustom::random_str(20);

        return "temp/{$str}";
    }


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

}