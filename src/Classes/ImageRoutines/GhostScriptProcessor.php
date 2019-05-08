<?php

namespace Omadonex\LaravelSupport\Classes\ImageRoutines;

use Omadonex\LaravelSupport\Classes\ShellProcessor\ShellProcessor;

class GhostScriptProcessor extends ShellProcessor
{
    /**
     * @param $pathInputPdf
     * @param $pathOutputJpg
     * @param int $resolution
     * @return array
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function convertPdfToJpg($pathInputPdf, $pathOutputJpg, $resolution = 72)
    {
        $command = sprintf('gs -dBATCH -dNOPAUSE -sDEVICE=jpeg -r%d -sOutputFile=%s %s', $resolution, $pathOutputJpg, $pathInputPdf);

        return self::call($command);
    }

    /**
     * @param $pathFile
     * @param $outputFolder
     * @return array
     * @throws \Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException
     */
    public static function splitPDF($pathFile, $outputFolder)
    {
        $command = sprintf('gs -q -dNODISPLAY -c "(%s) (r) file runpdfbegin pdfpagecount = quit"', $pathFile);
        $output = self::call($command);
        $countPages = (int) $output[0];
        $data = [
            'count' => $countPages,
            'pathArray' => [],
        ];
        for ($i = 0; $i < $countPages; $i++) {
            $pathOutput = "{$outputFolder}/{$i}.pdf";
            $command = sprintf("gs -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -dSAFER -dFirstPage=%d -dLastPage=%d -sOutputFile=%s %s", $i + 1, $i + 1, $pathOutput, $pathFile);
            self::call($command);
            $data['pathArray'][] = $pathOutput;
        }

        return $data;
    }
}