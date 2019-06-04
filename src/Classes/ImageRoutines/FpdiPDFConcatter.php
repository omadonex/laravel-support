<?php

namespace App\Services;

use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;
use setasign\Fpdi\Fpdi;

class FpdiPDFConcatter extends Fpdi
{
    public function concatFiles($files, $dest = 'S', $output = '')
    {
        foreach ($files AS $file) {
            $pageCount = $this->setSourceFile($file);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $pageId = $this->ImportPage($pageNo);
                $s = $this->getTemplatesize($pageId);
                $this->AddPage($s['orientation'], $s);
                $this->useImportedPage($pageId);
            }
        }

        return $this->Output($dest, $output);
    }

    public function concatContents($pagesContents, $tempAbsolutePath, $dest = 'S', $output = '')
    {
        $tempPath = "{$tempAbsolutePath}/pdf-concat-" . UtilsCustom::random_str(5);
        @mkdir($tempPath);
        $index = 0;
        $files = [];
        foreach ($pagesContents as $pageContents) {
            $path = "{$tempPath}/{$index}.pdf";
            file_put_contents($path, $pageContents);
            $files[] = $path;
            $index++;
        }

        $result = $this->concatFiles($files, $dest, $output);
        UtilsCustom::removeDir($tempPath);

        return $result;
    }
}