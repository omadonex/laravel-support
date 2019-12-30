<?php

namespace Omadonex\LaravelSupport\Classes\ImageRoutines;

use Illuminate\Support\Facades\Storage;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class PDFUtilities
{
    /**
     * @return string
     */
    private static function getTempFolder()
    {
        $str = UtilsCustom::random_str(20);

        return "temp/{$str}";
    }

    public static function split($input, $file = false)
    {
        $folder = self::getTempFolder();
        if (!$file) {
            $path = storage_path("app/{$folder}/input.pdf");
            Storage::disk('local')->put("{$folder}/input.pdf", $input);
        } else {
            $path = $input;
        }

        return PDFProcessor::split($path, storage_path("app/{$folder}"));
    }

    public static function convertToJpg($input, $file = false)
    {

    }

    /**
     * @param $contents
     * @param $w
     * @param $h
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function resize($contents, $w, $h)
    {
        $folder = self::getTempFolder();
        $inputPath = storage_path("app/{$folder}/input.pdf");
        $outputPath = storage_path("app/{$folder}/output.pdf");
        Storage::disk('local')->put("{$folder}/input.pdf", $contents);

        PDFProcessor::resize($inputPath, $outputPath, $w, $h);
        $resultContents = Storage::disk('local')->get("{$folder}/output.pdf");
        Storage::disk('local')->deleteDirectory($folder);

        return $resultContents;
    }

    //TODO omadonex: ГОВНО МЕТОД ПОРТИТ PDF НАДО ПЕРЕДЕЛАТЬ (ImageMagick подменяет шприфты)
    /**
     * @param $contents
     * @param $wPix
     * @param $hPix
     * @param $xPix
     * @param $yPix
     * @return array
     * @throws \ImagickException
     */
    public static function crop($contents, $wPix, $hPix, $xPix, $yPix)
    {
        $resultContents = ImagickUtilities::convertToJpg($contents);
        $resultContents = ImagickUtilities::crop($resultContents, $wPix, $hPix, $xPix, $yPix);

        return ImagickUtilities::convertToPDF($resultContents);
    }

    //TODO omadonex: ГОВНО МЕТОД ПОРТИТ PDF НАДО ПЕРЕДЕЛАТЬ (ImageMagick подменяет шприфты)
    /**
     * @param $contents
     * @param $degrees
     * @return array
     * @throws \ImagickException
     */
    public static function rotate($contents, $degrees)
    {
        return ImagickUtilities::rotate($contents, $degrees);
    }

    //TODO omadonex: ГОВНО МЕТОД ПОРТИТ PDF НАДО ПЕРЕДЕЛАТЬ (ImageMagick подменяет шприфты)
    /**
     * @param $contents
     * @param $cuttingFieldsSize
     * @return array
     * @throws \ImagickException
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function drawCuttingFields($contents, $cuttingFieldsSize)
    {
        $resultContents = ImagickUtilities::convertToJpg($contents);
        $resultContents = ImagickUtilities::drawCuttingFields($resultContents, $cuttingFieldsSize);

        return ImagickUtilities::convertToPDF($resultContents);
    }

    //TODO omadonex: НЕПОНЯТНО КОРРЕКТНО РАБОТАЕТ ИЛИ НЕТ (ImageMagick подменяет шприфты)
    /**
     * @param $contents
     * @param $colorspace
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function convertToColorspace($contents, $colorspace)
    {
        return ImagickUtilities::convertToColorspace($contents, $colorspace);
    }
}
