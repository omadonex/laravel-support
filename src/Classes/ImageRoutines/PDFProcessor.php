<?php

namespace Omadonex\LaravelSupport\Classes\ImageRoutines;

use Omadonex\LaravelSupport\Classes\ShellProcessor\ShellProcessor;

class PDFProcessor extends ShellProcessor
{
    /**
     * @param $input
     * @param $output
     * @param $w
     * @param $h
     * @return mixed
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function resize($input, $output, $w, $h)
    {
        $command = "pdfscale -r \"custom mm {$w} {$h}\" -f disable {$input} {$output}";

        return self::call($command);
    }

    /**
     * @param $input
     * @param $outputFolder
     * @return array
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function split($input, $outputFolder)
    {
        $command = "gs -q -dNODISPLAY -c '({$input}) (r) file runpdfbegin pdfpagecount = quit'";
        $output = self::call($command);
        $countPages = (int) $output[0];
        $data = [
            'count' => $countPages,
            'folder' => $outputFolder,
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