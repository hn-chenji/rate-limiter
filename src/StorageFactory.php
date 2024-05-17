<?php
namespace RateLimiter;
use RateLimiter\Storage\FileStorage;
use RateLimiter\Storage\RedisStorage;
use RateLimiter\Storage\StorageInterface;

abstract class StorageFactory{
    public static function create(string $type, $ext): StorageInterface
    {
        return match ($type) {
            'file' => new FileStorage($ext),
            'redis' => new RedisStorage($ext),
            default => new FileStorage("/tmp/ratelimit")
        };
    }

}