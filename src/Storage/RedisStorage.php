<?php

namespace RateLimiter\Storage;

class RedisStorage implements StorageInterface {
    private $redis;

    public function __construct(\Redis $redis) {
        $this->redis = $redis;
    }

    public function get($key) {
        $value = $this->redis->get($key);
        return $value === false ? null : json_decode($value, true);
    }

    public function set($key, $value) {
        $this->redis->set($key, json_encode($value));
    }

    public function delete($key)
    {
        $this->redis->del($key);
    }
}
