<?php

namespace Omadonex\LaravelSupport\Services\FileStorage;

use Omadonex\LaravelSupport\Interfaces\FileStorage\IFileStorageAdapter;
use Illuminate\Support\Facades\Storage;

abstract class FileStorageAdapterBase implements IFileStorageAdapter
{
    const DISK_KEY_LOCAL = 'local';
    const DISK_KEY_CLOUD = 'cloud';

    protected $cloud;
    protected $disks;
    protected $storageClass;

    public function __construct($cloud = false, $storageClass = 'COLD')
    {
        $this->cloud = $cloud;
        $key = $cloud ? self::DISK_KEY_CLOUD : self::DISK_KEY_LOCAL;
        $this->disks = $this->getStorageDiskArray()[$key];
        $this->storageClass = $storageClass;
    }

    abstract protected function getStorageDiskArray();

    private function getStorage($disk)
    {
        return Storage::disk($this->disks[$disk]);
    }

    public function put($disk, $filename, $contents, $cold = true)
    {
        if ($this->cloud && $cold) {
            $this->getStorage($disk)->getDriver()->put($filename, $contents, ['StorageClass' => $this->storageClass]);
        } else {
            $this->getStorage($disk)->put($filename, $contents);
        }
    }

    public function delete($disk, $filename)
    {
        return $this->getStorage($disk)->delete($filename);
    }

    public function has($disk, $filename)
    {
        return $this->getStorage($disk)->has($filename);
    }

    public function get($disk, $filename)
    {
        return $this->getStorage($disk)->get($filename);
    }

    public function size($disk, $filename)
    {
        return $this->getStorage($disk)->size($filename);
    }

    public function copy($disk, $filenameOld, $filenameNew)
    {
        $this->getStorage($disk)->copy($filenameOld, $filenameNew);
    }

    public function move($disk, $filenameOld, $filenameNew)
    {
        $this->getStorage($disk)->move($filenameOld, $filenameNew);
    }

    public function url($disk, $filename)
    {
        return $this->getStorage($disk)->url($filename);
    }

    public function makeDirectory($disk, $directory)
    {
        $this->getStorage($disk)->makeDirectory($directory);
    }

    public function deleteDirectory($disk, $directory)
    {
        $this->getStorage($disk)->deleteDirectory($directory);
    }

    public function s3GetPresignedUrl($disk, $filename, $s3Command = 'PutObject', $expires = 30)
    {
        if (!$this->cloud) {
            return null;
        }

        $adapter = $this->getStorage($disk)->getAdapter();
        if (get_class($adapter) !== 'League\Flysystem\AwsS3v3\AwsS3Adapter') {
            return null;
        }

        $s3Bucket = $adapter->getBucket();
        $s3Client = $adapter->getClient();

        $cmd = $s3Client->getCommand($s3Command, [
            'Bucket' => $s3Bucket,
            'Key' => $filename,
        ]);

        $presignedRequest = $s3Client->createPresignedRequest($cmd, "+{$expires} minutes");

        return (string)$presignedRequest->getUri();
    }

    public function s3DoesObjectExist($disk, $filename)
    {
        if (!$this->cloud) {
            return null;
        }

        $adapter = $this->getStorage($disk)->getAdapter();
        if (get_class($adapter) !== 'League\Flysystem\AwsS3v3\AwsS3Adapter') {
            return null;
        }

        $s3Bucket = $adapter->getBucket();
        $s3Client = $adapter->getClient();

        return $s3Client->doesObjectExist($s3Bucket, $filename);
    }
}
