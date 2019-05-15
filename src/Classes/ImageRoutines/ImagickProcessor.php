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
    public static function makeSRGBPreviewWithCloseColors($input, $output, $colorspaceName, $profile, $profileSRGB, $resolution = null)
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
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function identify($input, $verbose = false)
    {
        $verboseStr = $verbose ? '-verbose ' : '';
        $command = "identify {$verboseStr}{$input}";

        return self::call($command);
    }
}