<?php

namespace Omadonex\LaravelSupport\Classes\FileStorage;

use Ramsey\Uuid\Uuid;

class FileMeta
{
    private $disk;
    private $directory;
    private $uuid;

    public function __construct($directory = null, $uuid = null, $disk = null)
    {
        $this->disk = $disk;
        $this->directory = $directory;
        $this->uuid = $uuid ?: Uuid::uuid4()->toString();
    }

    public function setDisk($disk)
    {
        $this->disk = $disk;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    public function getDisk()
    {
        return $this->disk;
    }

    public function getPath()
    {
        $path = $this->uuid;

        if ($this->directory) {
            $path = "{$this->directory}/{$path}";
        }

        return $path;
    }
}
