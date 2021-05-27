<?php

namespace Omadonex\LaravelSupport\Classes\ImageRoutines;

use Illuminate\Support\Facades\Storage;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class ImagickUtilities
{
    /**
     * @param $contents
     * @return \Imagick
     * @throws \ImagickException
     */
    private static function loadInstance($contents)
    {
        $img = new \Imagick;
        $img->readImageBlob($contents);

        return $img;
    }

    /**
     * @param $img
     * @param $callback
     * @param bool $all
     * @param int $index
     * @return array
     */
    private static function process($img, $callback, $all, $index)
    {
        if ($all) {
            $result = [];
            $countImages = $img->getNumberImages();
            for ($i = 0; $i < $countImages; $i++) {
                $img->setIteratorIndex($i);
                $result[] = $callback($img, $i);
            }

            return $result;
        }

        $img->setIteratorIndex($index);

        return $callback($img, $index);
    }

    /**
     * @param $contents
     * @return string|null
     */
    private static function getImageFormat($contents)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($contents);

        switch ($mimeType) {
            case 'image/jpeg': return 'jpg';
            case 'image/vnd.adobe.photoshop': return 'psd';
        }

        return null;
    }

    /**
     * @return string
     */
    private static function getTempFolder()
    {
        $str = UtilsCustom::random_str(20);

        return "temp/{$str}";
    }

    /**
     * @param $contents
     * @param bool $strict
     * @param bool $all
     * @param int $index
     * @return array
     * @throws \ImagickException
     */
    public static function determineParams($contents, $strict = true, $all = false, $index = 0)
    {
        $img = self::loadInstance($contents);

        $processResult = self::process($img, function ($instance, $iterator) use ($strict, $contents) {
            $resolution = $instance->getImageResolution();
            $xDpi = (int) $resolution['x'];
            $yDpi = (int) $resolution['y'];

            $dpi = null;
            if ($strict) {
                if (($xDpi === 0) || ($yDpi === 0) || ($xDpi !== $yDpi)) {

                    $folder     = UtilsCustom::random_str(20);
                    $inputPath  = storage_path("app/{$folder}/input.pdf");
                    Storage::disk('local')->put("{$folder}/input.pdf", $contents);
                    exec("identify -quiet -format '%x' $inputPath", $data);
                    Storage::disk('local')->deleteDirectory($folder);

                    if($data && is_array($data)){
                        $data = explode(' ', $data[0]);

                        if (count($data) > 1) {
                            if($data[1] == 'PixelsPerInch'){
                                $dpi = $data[0];
                            }elseif($data[1] == 'PixelsPerCentimeter'){
                                $dpi = ceil($data[0] * 2.54);
                            }elseif($data[1] == 'Undefined'){
                                $dpi = $data[0];
                            }
                        } elseif (count($data) === 1 && is_numeric((int) $data[0])) {
                            $dpi = (int) $data[0];
                        }
                    }

                    if (is_null($dpi) || $dpi <= 0) {
                        return null;
                    } else {
                        $xDpi = $resolution['x'] = $resolution['y'] = $dpi;
                    }
                }

                $dpi = $xDpi;
            }

            return [
                'wPix' => $instance->getImageWidth(),
                'hPix' => $instance->getImageHeight(),
                'xDpi' => $resolution['x'],
                'yDpi' => $resolution['y'],
                'dpi' => $dpi,
            ];
        }, $all, $index);

        $img->clear();

        return $processResult;
    }

    /**
     * @param $contents
     * @param $degrees
     * @param bool $all
     * @param int $index
     * @return array
     * @throws \ImagickException
     */
    public static function rotate($contents, $degrees, $all = false, $index = 0)
    {
        $img = self::loadInstance($contents);

        $processResult = self::process($img, function ($instance, $iterator) use ($degrees) {
            $instance->rotateImage(new \ImagickPixel(), $degrees);

            return $instance->getImageBlob();
        }, $all, $index);

        $img->clear();

        return $processResult;
    }

    /**
     * @param $contents
     * @param $w
     * @param $h
     * @return string
     * @throws \ImagickException
     */
    public static function scale($contents, $w, $h)
    {
        //Скалируем только нулевой слой, т.к. в случае с несколькими слоями он является слитым
        $params = self::determineParams($contents, true, false);

        $scaleCalculator = new ScaleCalculator($params['wPix'], $params['hPix'], $params['dpi']);
        $scaleData = $scaleCalculator->getScaleData($w, $h);

        $scaledDpi = $scaleData['scaled']['dpi'];
        $scaledWPix = $params['wPix'] + $scaleData['adjust']['wPix'];
        $scaledHPix = $params['hPix'] + $scaleData['adjust']['hPix'];

        $img = self::loadInstance($contents);
        $img->setIteratorIndex(0);
        $img->setImageResolution($scaledDpi, $scaledDpi);
        $img->scaleImage($scaledWPix, $scaledHPix);
        $resultContents = $img->getImageBlob();

        $img->clear();

        return $resultContents;
    }

    /**
     * @param $contents
     * @param $wPix
     * @param $hPix
     * @param $xPix
     * @param $yPix
     * @param bool $all
     * @param int $index
     * @return array
     * @throws \ImagickException
     */
    public static function crop($contents, $wPix, $hPix, $xPix, $yPix, $all = false, $index = 0)
    {
        $img = self::loadInstance($contents);

        $processResult = self::process($img, function ($instance, $iterator) use ($wPix, $hPix, $xPix, $yPix) {
            $instance->cropImage($wPix, $hPix, $xPix, $yPix);

            return $instance->getImageBlob();
        }, $all, $index);

        $img->clear();

        return $processResult;
    }

    /**
     * @param $contents
     * @param $colorspace
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function convertToColorspace($contents, $colorspace)
    {
        $folder = self::getTempFolder();
        $inputPath = storage_path("app/{$folder}/input");
        $outputPath = storage_path("app/{$folder}/output");
        Storage::disk('local')->put("{$folder}/input", $contents);

        $colorspaceName = self::getColorspaceName($colorspace);
        $colorspaceProfile = self::getProfileByColorspace($colorspace);
        ImagickProcessor::convertToColorspace($inputPath, $outputPath, $colorspaceName, $colorspaceProfile);
        $resultContents = Storage::disk('local')->get("{$folder}/output");
        Storage::disk('local')->deleteDirectory($folder);

        return $resultContents;
    }

    /**
     * @param $contents
     * @param null $resolution
     * @param bool $all
     * @param int $index
     * @return array
     * @throws \ImagickException
     */
    public static function makePreview($contents, $resolution = null, $all = false, $index = 0)
    {
        $img = self::loadInstance($contents);

        $processResult = self::process($img, function ($instance, $iterator) use ($resolution) {
            $instance->setImageFormat('jpg');

            if ($resolution) {
                $instance->resampleImage($resolution, $resolution, \Imagick::FILTER_UNDEFINED, 1);
            }

            return $instance->getImageBlob();
        }, $all, $index);

        $img->clear();

        return $processResult;
    }

    /**
     * @param $contents
     * @param $colorspace
     * @param null $resolution
     * @param bool $all
     * @param int $index
     * @return array
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function makeSRGBPreviewWithCloseColors($contents, $colorspace, $resolution = null, $all = false, $index = 0)
    {
        $folder = self::getTempFolder();
        $inputPath = storage_path("app/{$folder}/input");
        $outputPathSuffix = $all ? '' : "-{$index}";
        $outputPath = storage_path("app/{$folder}/output{$outputPathSuffix}");
        Storage::disk('local')->put("{$folder}/input", $contents);

        $countImages = count(ImagickProcessor::identify($inputPath));
        if (($countImages > 1) && !$all) {
            $inputPath .= "[{$index}]";
        }

        $colorspaceName = self::getColorspaceName($colorspace);
        $colorspaceProfile = self::getProfileByColorspace($colorspace);
        $profileSRGB = self::getProfileByColorspace(\Imagick::COLORSPACE_SRGB);
        ImagickProcessor::makeSRGBPreviewWithCloseColors($inputPath, $outputPath, $colorspaceName, $colorspaceProfile, $profileSRGB, $resolution);

        if ($all) {
            $resultContents = [];
            for ($i = 0; $i < $countImages; $i++) {
                $resultContents[] = Storage::disk('local')->get("{$folder}/output{$outputPathSuffix}-{$i}");
            }
        } else {
            $resultContents = Storage::disk('local')->get("{$folder}/output{$outputPathSuffix}");
        }

        Storage::disk('local')->deleteDirectory($folder);

        return $resultContents;
    }

    /**
     * @param $contents
     * @param $cuttingFieldsSize
     * @return mixed
     * @throws \ImagickException
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function drawCuttingFields($contents, $cuttingFieldsSize)
    {
        $folder = self::getTempFolder();
        $inputPath = storage_path("app/{$folder}/input");
        $outputPath = storage_path("app/{$folder}/output");
        Storage::disk('local')->put("{$folder}/input", $contents);

        $params = self::determineParams($contents, true, false);
        $fieldsPix = ScaleCalculator::toPix($cuttingFieldsSize, $params['dpi']);
        //Рисуем только на нулевом слое, т.к. дорисовывать поля везде нет возможности
        try {
            ImagickProcessor::drawCuttingFields("{$inputPath}[0]", $outputPath, [
                'width' => $params['wPix'] + 2 * $fieldsPix,
                'height' => $params['hPix'] + 2 * $fieldsPix,
                'fields' => $fieldsPix,
            ]);
        } catch (\Exception $e) {

        }

        $resultContents = Storage::disk('local')->get("{$folder}/output");
        Storage::disk('local')->deleteDirectory($folder);

        return $resultContents;
    }

    /**
     * @param $contents
     * @param bool $returnPath
     * @param bool $all
     * @param int $index
     * @return array
     * @throws \ImagickException
     */
    public static function convertToPDF($contents, $returnPath = false, $all = false, $index = 0)
    {
        $img = self::loadInstance($contents);

        $processResult = self::process($img, function ($instance, $iterator) use ($returnPath) {
            $instance->setFormat('pdf');

            $folder    = self::getTempFolder();
            $inputPath = storage_path("app/{$folder}/input.pdf");
            mkdir(storage_path("app/{$folder}"));
            $instance->writeImage($inputPath);
            $content = file_get_contents($inputPath);
            Storage::disk('local')->deleteDirectory($folder);

            return $content;
        }, $all, $index);

        $img->clear();

        return $processResult;
    }

    /**
     * @param $contents
     * @return int
     * @throws \ImagickException
     */
    public static function getColorspace($contents)
    {
        $img = self::loadInstance($contents);
        $colorspace = $img->getImageColorspace();
        $img->clear();

        return $colorspace;
    }

    /**
     * @param $colorspace
     * @return string
     */
    public static function getColorspaceName($colorspace)
    {
        switch ($colorspace) {
            case \Imagick::COLORSPACE_RGB: return 'RGB';
            case \Imagick::COLORSPACE_GRAY: return 'GRAY';
            case \Imagick::COLORSPACE_TRANSPARENT: return 'TRANSPARENT';
            case \Imagick::COLORSPACE_OHTA: return 'OHTA';
            case \Imagick::COLORSPACE_LAB: return 'LAB';
            case \Imagick::COLORSPACE_XYZ: return 'XYZ';
            case \Imagick::COLORSPACE_YCBCR: return 'YCBCR';
            case \Imagick::COLORSPACE_YCC: return 'YCC';
            case \Imagick::COLORSPACE_YIQ: return 'YIQ';
            case \Imagick::COLORSPACE_YPBPR: return 'YPBPR';
            case \Imagick::COLORSPACE_YUV: return 'YUV';
            case \Imagick::COLORSPACE_CMYK: return 'CMYK';
            case \Imagick::COLORSPACE_SRGB: return 'SRGB';
            case \Imagick::COLORSPACE_HSB: return 'HSB';
            case \Imagick::COLORSPACE_HSL: return 'HSL';
            case \Imagick::COLORSPACE_HWB: return 'HWB';
            case \Imagick::COLORSPACE_REC601LUMA: return 'REC601LUMA';
            case \Imagick::COLORSPACE_REC709LUMA: return 'REC709LUMA';
            case \Imagick::COLORSPACE_LOG: return 'LOG';
            default: return 'UNDEFINED';
        }
    }

    /**
     * @param $colorspace
     * @param bool $getContents
     * @return false|string|null
     */
    public static function getProfileByColorspace($colorspace, $getContents = false)
    {
        $path = null;
        switch ($colorspace) {
            case \Imagick::COLORSPACE_CMYK: $path = base_path('vendor/omadonex/laravel-support/resources/profiles/coated.icc'); break;
            case \Imagick::COLORSPACE_SRGB: $path = base_path('vendor/omadonex/laravel-support/resources/profiles/srgb.icc'); break;
        }

        if (is_null($path)) {
            return null;
        }

        return $getContents ? file_get_contents($path) : $path;
    }
}
