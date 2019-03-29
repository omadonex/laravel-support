<?php

namespace Omadonex\LaravelSupport\Traits;

use Illuminate\Support\Facades\Log;

trait LoggingTrait
{
    protected $logging = false;

    public function enableLogging()
    {
        $this->logging = true;
    }

    public function disableLogging()
    {
        $this->logging = false;
    }

    protected function log($message, $level = 'info')
    {
        $className = (new \ReflectionClass($this))->getShortName();
        if ($this->logging) {
            Log::$level("{$className} :: {$message}");
        }
    }
}
