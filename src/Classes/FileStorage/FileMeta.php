<?php

namespace Omadonex\LaravelSupport\Classes\FileStorage;

use Ramsey\Uuid\Uuid;

class FileMeta
{
    private $disk;
    private $directory;
    private $uuid;
    private $suffix;

    public function __construct($directory = null, $uuid = null, $suffix = '', $disk = null)
    {
        $this->disk = $disk;
        $this->directory = $directory;
        $this->uuid = $uuid ?: Uuid::uuid4()->toString();
        $this->suffix = $suffix;
    }

    public function setDisk($disk)
    {
        $this->disk = $disk;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    public function getDisk()
    {
        return $this->disk;
    }

    public function getPath()
    {
        $path = "{$this->uuid}{$this->suffix}";

        if ($this->directory) {
            $path = "{$this->directory}/{$path}";
        }

        return $path;
    }
}
