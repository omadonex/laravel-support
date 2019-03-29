<?php

namespace Omadonex\LaravelSupport\Classes\Utils;

use Illuminate\Support\Str;

class UtilsCustom
{
    public static function random_str($length,
        $keySpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keySpace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keySpace[random_int(0, $max)];
        }
        return $str;
    }

    public static function timezoneList()
    {
        $timezoneIdentifiers = \DateTimeZone::listIdentifiers();
        $utcTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $tempTimezones = array_map(function ($timezoneIdentifier) use ($utcTime) {
            $currentTimezone = new \DateTimeZone($timezoneIdentifier);
            return [
                'offset' => (int)$currentTimezone->getOffset($utcTime),
                'identifier' => $timezoneIdentifier
            ];
        }, $timezoneIdentifiers);

        // Sort the array by offset,identifier ascending
        usort($tempTimezones, function($a, $b) {
            return ($a['offset'] == $b['offset'])
                ? strcmp($a['identifier'], $b['identifier'])
                : $a['offset'] - $b['offset'];
        });

        return $tempTimezones;
    }

    private static function addSourceToZip($zip, $source, $constraints, $createSubDirs)
    {
        if (is_file($source)) {
            $zip->addFile($source, basename($source));
        } elseif (is_dir($source)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            $applyConstraints = !empty($constraints);
            $constraintMode = null;
            if ($applyConstraints) {
                $constraintMode = array_keys($constraints)[0];
                if (!in_array($constraintMode, ['except', 'only'])) {
                    $applyConstraints = false;
                    $constraintMode = null;
                }
            }

            foreach ($files as $name => $file) {
                $include = true;
                if (!$file->isDir() && !in_array($file->getBasename(), ['.', '..'])) {
                    $absolutePath = $file->getRealPath();
                    if ($applyConstraints) {
                        if ($constraintMode === 'except') {
                            $c = $constraints[$constraintMode];

                            if (!empty($c['files']) && in_array($absolutePath, $c['files'])) {
                                $include = false;
                            }

                            if (!empty($c['directories'])) {
                                foreach ($c['directories'] as $directory) {
                                    if (is_dir($directory) && strpos($absolutePath, $directory) !== false) {
                                        $include = false;
                                        break;
                                    }
                                }
                            }
                        } elseif ($constraintMode === 'only') {
                            //TODO omadonex
                            $include = true;
                        }
                    }

                    if ($include) {
                        $relativePath = substr($absolutePath, strlen($source) + 1);
                        // Add current file to archive
                        $zip->addFile($absolutePath, $relativePath);
                    }
                }
            }
        }
    }

    public static function zip($source, $destination, $finalDestination = null, $constraints = [], $createSubDirs = true)
    {
        $dirName = dirname($destination);
        if (!file_exists($dirName)) {
            mkdir($dirName, 0777, true);
        }

        $zip = new \ZipArchive();
        $zip->open($destination, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        UtilsCustom::addSourceToZip($zip, $source, $constraints, $createSubDirs);

        $zip->close();

        if ($finalDestination && ($finalDestination !== $destination)) {
            rename($destination, $finalDestination);
        }
    }

    public static function unzip($source, $destination)
    {
        $zip = new \ZipArchive();
        $zip->open($source);
        $zip->extractTo($destination);
        $zip->close();
    }

    public static function copyDir($source, $destination) {
        $dir = opendir($source);
        @mkdir($destination);
        while (false !== ( $file = readdir($dir)) ) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir("{$source}/{$file}") ) {
                    self::copyDir("{$source}/{$file}","{$destination}/{$file}");
                } else {
                    copy("{$source}/{$file}","{$destination}/{$file}");
                }
            }
        }
        closedir($dir);
    }

    public static function removeDir($source) {
        $dir = opendir($source);
        while(false !== ( $file = readdir($dir)) ) {
            if (($file != '.') && ($file != '..')) {
                $full = "{$source}/{$file}";
                if (is_dir($full) ) {
                    self::removeDir($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($source);
    }

    public static function unlinkIf($filename)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public static function createTableNameFromModelName($modelName)
    {
        $pieces = preg_split('/(?=[A-Z])/', $modelName, -1, PREG_SPLIT_NO_EMPTY);

        $string = '';
        foreach ($pieces as $i => $piece) {
            if ($i+1 < count($pieces)) {
                $string .= strtolower($piece) . '_';
            } else {
                $string .= Str::plural(strtolower($piece));
            }
        }

        return $string;
    }

    public static function getCamelName($dotName)
    {
        $dotParts = explode('.', $dotName);
        $countParts = count($dotParts);
        $name = $dotParts[0];

        for ($i = 1; $i < $countParts; $i++) {
            $name .= ucfirst($dotParts[$i]);
        }

        return $name;
    }

    public static function deepScandir($path, $recursive = true, $nameModifierCallback = null, $ignore = [], $subfolder = null)
    {
        //TODO omadonex: для $ignore стоит сделать регулярные выражения, сейчас тупо сравниваем по имени

        $out = [];

        $files = scandir($path);
        foreach ($files as $file) {
            if (($file === '.') || ($file === '..') || in_array(basename($file), $ignore)) {
                continue;
            }

            $filename = "{$path}/{$file}";
            if (is_dir($filename)) {
                if ($recursive) {
                    $subfolderPart = basename($filename);
                    $newSubfolder = $subfolder ? "{$subfolder}/{$subfolderPart}" : $subfolderPart;
                    $out = array_merge($out, self::deepScandir($filename, $recursive, $nameModifierCallback, $ignore, $newSubfolder));
                }
            } else {
                $name = is_callable($nameModifierCallback) ? $nameModifierCallback($filename) : $filename;
                $out[] = [
                    'subfolder' => $subfolder,
                    'file' => $name,
                ];
            }
        }

        return $out;
    }

    public static function swapValues(&$x, &$y)
    {
        $tmp = $x;
        $x = $y;
        $y = $tmp;
    }
}
