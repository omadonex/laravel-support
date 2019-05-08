<?php

namespace Omadonex\LaravelSupport\Classes\ImageRoutines;

use Illuminate\Support\Facades\Storage;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class ImagickUtilities
{
    private static function loadInstance($contents)
    {
        $img = new \Imagick;
        $img->readImageBlob($contents);

        return $img;
    }

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

    public static function scale($contents, $w, $h)
    {
        $img = self::loadInstance($contents);
        $width = $img->getImageWidth();
        $height = $img->getImageHeight();
        $resolution = $img->getImageResolution();
        if ($resolution['x'] != $resolution['y']) {
            //TODO omadonex: нормальный Exception
            throw new \Exception();
        }
        $dpi = $resolution['x'];


        $scaleCalculator = new ScaleCalculator($width, $height, $dpi);
        $scaleData = $scaleCalculator->getScaleData($w, $h);

        $scaledDpi = $scaleData['scaled']['dpi'];
        $scaledWPix = $width + $scaleData['adjust']['wPix'];
        $scaledHPix = $height + $scaleData['adjust']['hPix'];

        $img->setImageResolution($scaledDpi, $scaledDpi);
        $img->scaleImage($scaledWPix, $scaledHPix);
        $resultContents = $img->getImageBlob();
        $img->destroy();

        return $resultContents;
    }

    public static function determineParams($contents)
    {
        $img = self::loadInstance($contents);
        $resolution = $img->getImageResolution();
        $xDpi = $resolution['x'];
        $yDpi = $resolution['y'];
        if (($xDpi == 0) || ($yDpi == 0) || ($xDpi !== $yDpi)) {
            return null;
        }

        $wPix = $img->getImageWidth();
        $hPix = $img->getImageHeight();

        $img->destroy();

        return [
            'wPix' => $wPix,
            'hPix' => $hPix,
            'dpi' => $xDpi,
        ];
    }

    public static function crop($contents, $wPix, $hPix, $xPix, $yPix)
    {
        $img = self::loadInstance($contents);
        $img->cropImage($wPix, $hPix, $xPix, $yPix);
        $resultContents = $img->getImageBlob();
        $img->destroy();

        return $resultContents;
    }

    private static function getTempFolder()
    {
        $str = UtilsCustom::random_str(20);

        return "temp/{$str}";
    }

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

    public static function makePreview($contents, $resolution = null)
    {
        $img = self::loadInstance($contents);
        $img->setImageFormat('jpg');

        if ($resolution) {
            $img->resampleImage($resolution, $resolution, \Imagick::FILTER_UNDEFINED, 1);
        }

        $resultContents = $img->getImageBlob();
        $img->destroy();

        return $resultContents;
    }

    public static function makeSRGBPreviewWithCloseColors($contents, $colorspace, $resolution = null)
    {
        $folder = self::getTempFolder();
        $inputPath = storage_path("app/{$folder}/input");
        $outputPath = storage_path("app/{$folder}/output");
        Storage::disk('local')->put("{$folder}/input", $contents);

        $colorspaceName = self::getColorspaceName($colorspace);
        $colorspaceProfile = self::getProfileByColorspace($colorspace);
        $profileSRGB = self::getProfileByColorspace(\Imagick::COLORSPACE_SRGB);
        ImagickProcessor::makeSRGBPreviewWithCloseColors($inputPath, $outputPath, $colorspaceName, $colorspaceProfile, $profileSRGB, $resolution);
        $resultContents = Storage::disk('local')->get("{$folder}/output");
        Storage::disk('local')->deleteDirectory($folder);

        return $resultContents;
    }

    public static function drawCuttingFields($contents, $cuttingFieldsSize)
    {
        $folder = self::getTempFolder();
        $inputPath = storage_path("app/{$folder}/input");
        $outputPath = storage_path("app/{$folder}/output");
        Storage::disk('local')->put("{$folder}/input", $contents);

        $params = self::determineParams($contents);
        $fieldsPix = ScaleCalculator::toPix($cuttingFieldsSize, $params['dpi']);
        ImagickProcessor::drawCuttingFields($inputPath, $outputPath, [
            'width' => $params['wPix'] + 2 * $fieldsPix,
            'height' => $params['hPix'] + 2 * $fieldsPix,
            'fields' => $fieldsPix,
        ]);

        $resultContents = Storage::disk('local')->get("{$folder}/output");
        Storage::disk('local')->deleteDirectory($folder);

        return $resultContents;
    }

    public static function getColorspace($contents)
    {
        $img = self::loadInstance($contents);
        $colorspace = $img->getImageColorspace();
        $img->destroy();

        return $colorspace;
    }

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