<?php

namespace Omadonex\LaravelSupport\Classes\ImageRoutines;

use Omadonex\LaravelSupport\Classes\ShellProcessor\ShellProcessor;

class ImagickProcessor extends ShellProcessor
{
    /**
     * @param $input
     * @param $output
     * @param $colorspaceName
     * @param $profile
     * @return array
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function convertToColorspace($input, $output, $colorspaceName, $profile)
    {
        $command = sprintf("convert {$input} -colorspace {$colorspaceName} -profile {$profile} {$output}");

        return self::call($command);
    }

    /**
     * @param $input
     * @param $output
     * @param $colorspaceName
     * @param $profile
     * @param $profileSRGB
     * @return array
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function makeSRGBPreviewWithCloseColors($input, $output, $colorspaceName, $profile, $profileSRGB)
    {
        $command = sprintf("convert {$input} -colorspace {$colorspaceName} -profile {$profile} -profile {$profileSRGB} {$output}");

        return self::call($command);
    }

    /**
     * @param $input
     * @param $output
     * @param $payload
     * @return array
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function drawCuttingFields($input, $output, $payload)
    {
        $command = "convert {$input} -define distort:viewport={$payload['width']}x{$payload['height']}-{$payload['fields']}-{$payload['fields']} -filter point -distort SRT 0  +repage {$output}";

        return self::call($command);
    }
}