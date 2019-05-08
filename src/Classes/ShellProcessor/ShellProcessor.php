<?php

namespace Omadonex\LaravelSupport\Classes\ShellProcessor;

use Omadonex\LaravelSupport\Classes\Exceptions\OmxShellException;

class ShellProcessor
{
    protected static function call($command)
    {
        exec($command, $output, $result);

        if ($result !== 0) {
            throw new OmxShellException($result, $output);
        }

        return $output;
    }
}