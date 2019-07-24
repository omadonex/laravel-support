<?php

namespace Omadonex\LaravelSupport\Classes\ImageRoutines;

use Omadonex\LaravelSupport\Classes\ShellProcessor\ShellProcessor;

class GhostScriptProcessor extends ShellProcessor
{
    /**
     * @param $input
     * @param $output
     * @param null $resolution
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function convertToJpg($input, $output, $resolution = null)
    {
        if (is_null($resolution)) {
            $command = "gs -dBATCH -dNOPAUSE -sDEVICE=jpeg -sOutputFile={$output} {$input}";
        } else {
            $command = "gs -dBATCH -dNOPAUSE -sDEVICE=jpeg -r{$resolution} -sOutputFile={$output} {$input}";
        }

        return self::call($command);
    }

    /**
     * @param $inputPages
     * @param $output
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function mergePDF($inputPages, $output)
    {
        $pagesStr = implode(' ', $inputPages);
        $command = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile={$output} {$pagesStr}";

        return self::call($command);
    }
}