<?php

namespace RateLimiter\Storage;

class FileStorage implements StorageInterface
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    private function getFilePath($key)
    {
        return $this->path . DIRECTORY_SEPARATOR . md5($key) . '.json';
    }

    public function get($key)
    {
        $filePath = $this->getFilePath($key);

        $data = null;
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath), true);
        }

        return $data;
    }

    public function set($key, $value)
    {
        $filePath = $this->getFilePath($key);

        file_put_contents($filePath, json_encode($value), LOCK_EX);
    }

    public function delete($key) {
        $filePath = $this->getFilePath($key);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
