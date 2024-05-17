<?php

namespace RateLimiter\Strategy;

use RateLimiter\Storage\StorageInterface;

/**
 * 滑动窗口策略
 */
class SlidingWindowStrategy implements StrategyInterface {
    private $storage;
    private $limit; //限制次数
    private $window; //窗口大小

    public function __construct(StorageInterface $storage, int $limit, int $window) {
        $this->storage = $storage;
        $this->limit = $limit;
        $this->window = $window;
    }

    public function isRateLimited($key) {
        $currentTime = time();
        $windowStart = $currentTime - $this->window;

        $timestamps = $this->storage->get($key);
        if ($timestamps === null) {
            $timestamps = [];
        }

        // 过滤掉超出窗口范围的过期时间
        $timestamps = array_filter($timestamps, function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });

        if (count($timestamps) >= $this->limit) {
            return true;
        }

        $timestamps[] = $currentTime;
        $this->storage->set($key, $timestamps);
        return false;
    }
}
