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
    public static function makePreview($input, $output, $resolution = null)
    {
        if (is_null($resolution)) {
            $command = "gs -dBATCH -dNOPAUSE -sDEVICE=jpeg -sOutputFile={$output} {$input}";
        } else {
            $command = "gs -dBATCH -dNOPAUSE -sDEVICE=jpeg -r{$resolution} -sOutputFile={$output} {$input}";
        }

        return self::call($command);
    }

    /**
     * @param $input
     * @param $outputFolder
     * @return array
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function splitPDF($input, $outputFolder)
    {
        $command = "gs -q -dNODISPLAY -c '({$input}) (r) file runpdfbegin pdfpagecount = quit'";
        $output = self::call($command);
        $countPages = (int) $output[0];
        $data = [
            'count' => $countPages,
            'pathArray' => [],
        ];
        for ($i = 0; $i < $countPages; $i++) {
            $pathOutput = "{$outputFolder}/{$i}.pdf";
            $index = $i + 1;
            $command = "gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -dSAFER -dFirstPage={$index} -dLastPage={$index} -sOutputFile={$pathOutput} {$input}";
            self::call($command);
            $data['pathArray'][] = $pathOutput;
        }

        return $data;
    }
}