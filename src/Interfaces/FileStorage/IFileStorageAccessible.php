<?php

namespace Omadonex\LaravelSupport\Interfaces\FileStorage;

interface IFileStorageAccessible
{
    public function getFileMeta($params = []);
}