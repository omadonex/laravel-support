<?php

namespace Omadonex\LaravelSupport\Interfaces\FileStorage;

use Omadonex\LaravelSupport\Classes\FileStorage\FileMeta;

interface IFileStorageBase
{
    public function putContents(FileMeta $fileMeta, $contents, $cold = true);

    public function get(FileMeta $fileMeta);

    public function delete(FileMeta $fileMeta);

    public function has(FileMeta $fileMeta);

    public function size(FileMeta $fileMeta);

    public function url(FileMeta $fileMeta);

    public function s3GetPresignedUrl(FileMeta $fileMeta, $s3Command = 'PutObject', $expires = 30);

    public function s3DoesObjectExists(FileMeta $fileMeta);

    public function makeDirectory($disk, $directory);

    public function deleteDirectory($disk, $directory);
}