<?php

namespace Omadonex\LaravelSupport\Interfaces\FileStorage;

interface IFileStorageAdapter
{
    public function put($disk, $filename, $contents, $cold = true);

    public function has($disk, $filename);

    public function delete($disk, $filename);

    public function get($disk, $filename);

    public function size($disk, $filename);

    public function makeDirectory($disk, $directory);

    public function deleteDirectory($disk, $directory);

    public function url($disk, $filename);

    public function s3GetPresignedUrl($disk, $path, $s3Command = 'PutObject', $expires = 30);

    public function s3DoesObjectExist($disk, $filename);
}