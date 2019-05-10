<?php

namespace Omadonex\LaravelSupport\Services\FileStorage;

use Omadonex\LaravelSupport\Classes\FileStorage\FileMeta;
use Omadonex\LaravelSupport\Interfaces\FileStorage\IFileStorageAdapter;
use Omadonex\LaravelSupport\Interfaces\FileStorage\IFileStorageBase;

abstract class FileStorageBase implements IFileStorageBase
{
    protected $adapter;

    public function __construct(IFileStorageAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function putContents(FileMeta $fileMeta, $contents, $cold = true)
    {
        $this->adapter->put($fileMeta->getDisk(), $fileMeta->getPath(), $contents, $cold);

        return $fileMeta;
    }

    public function get(FileMeta $fileMeta)
    {
        return $this->adapter->get($fileMeta->getDisk(), $fileMeta->getPath());
    }

    public function delete(FileMeta $fileMeta)
    {
        return $this->adapter->delete($fileMeta->getDisk(), $fileMeta->getPath());
    }

    public function has(FileMeta $fileMeta)
    {
        return $this->adapter->has($fileMeta->getDisk(), $fileMeta->getPath());
    }

    public function size(FileMeta $fileMeta)
    {
        return $this->adapter->size($fileMeta->getDisk(), $fileMeta->getPath());
    }

    public function copy(FileMeta $fileMetaOld, FileMeta $fileMetaNew)
    {
        return $this->adapter->copy($fileMetaOld->getDisk(), $fileMetaOld->getPath(), $fileMetaNew->getPath());
    }

    public function move(FileMeta $fileMetaOld, FileMeta $fileMetaNew)
    {
        return $this->adapter->move($fileMetaOld->getDisk(), $fileMetaOld->getPath(), $fileMetaNew->getPath());
    }

    public function url(FileMeta $fileMeta)
    {
        return $this->adapter->url($fileMeta->getDisk(), $fileMeta->getPath());
    }

    public function s3GetPresignedUrl(FileMeta $fileMeta, $s3Command = 'PutObject', $expires = 30)
    {
        return $this->adapter->s3GetPresignedUrl($fileMeta->getDisk(), $fileMeta->getPath(), $s3Command, $expires);
    }

    public function s3DoesObjectExists(FileMeta $fileMeta)
    {
        return $this->adapter->s3DoesObjectExist($fileMeta->getDisk(), $fileMeta->getPath());
    }

    public function makeDirectory($disk, $directory)
    {
        $this->adapter->makeDirectory($disk, $directory);
    }

    public function deleteDirectory($disk, $directory)
    {
        $this->adapter->deleteDirectory($disk, $directory);
    }
}
