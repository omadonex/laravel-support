<?php

namespace Omadonex\LaravelSupport\Classes\ImageRoutines;

use Omadonex\LaravelSupport\Classes\ShellProcessor\ShellProcessor;

class ImagickProcessor extends ShellProcessor
{
    /**
     * @param $path
     * @param bool $allImages
     * @param int $imageIndex
     * @return string
     */
    private static function makePath($path, $allImages = true, $imageIndex = 0)
    {
        if ($allImages) {
            return $path;
        }

        return "{$path}[{$imageIndex}]";
    }

    /**
     * @param $input
     * @param $output
     * @param $colorspaceName
     * @param $profile
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function convertToColorspace($input, $output, $colorspaceName, $profile)
    {
        $command = "convert {$input} -colorspace {$colorspaceName} -profile {$profile} {$output}";

        return self::call($command);
    }

    /**
     * @param $input
     * @param $output
     * @param $colorspaceName
     * @param $profile
     * @param $profileSRGB
     * @param null $resolution
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function convertToJpgUsingColorspace($input, $output, $colorspaceName, $profile, $profileSRGB, $resolution = null)
    {
        if (is_null($resolution)) {
            $command = "convert {$input} -colorspace {$colorspaceName} -profile {$profile} -profile {$profileSRGB} jpg:{$output}";
        } else {
            $command = "convert -density {$resolution}x{$resolution} {$input} -colorspace {$colorspaceName} -profile {$profile} -profile {$profileSRGB} jpg:{$output}";
        }

        return self::call($command);
    }

    /**
     * @param $input
     * @param $output
     * @param $payload
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function drawCuttingFields($input, $output, $payload)
    {
        $command = "convert {$input} -define distort:viewport={$payload['width']}x{$payload['height']}-{$payload['fields']}-{$payload['fields']} -filter point -distort SRT 0  +repage {$output}";

        return self::call($command);
    }

    /**
     * @param $input
     * @param bool $verbose
     * @param bool $allImages
     * @param int $imageIndex
     * @return array
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function identify($input, $verbose = false, $allImages = true, $imageIndex = 0)
    {
        $inputPath = self::makePath($input, $allImages, $imageIndex);
        $verboseStr = $verbose ? '-verbose ' : '';
        $command = "identify {$verboseStr}{$inputPath}";
        $callOutput = self::call($command);

        if (!$allImages) {
            $callOutput = [explode('=>', $callOutput[0])[1]];
        }

        $data = [];
        foreach ($callOutput as $raw) {
            $rawData = explode(' ', $raw);
            $data[] = [
                'format' => $rawData[1],
                'dimension' => $rawData[2],
                'geometry' => $rawData[3],
                'depth' => $rawData[4],
                'colorspaceName' => $rawData[5],
                'size' => $rawData[6],
            ];
        }

        return $data;
    }
}