<?php

namespace RateLimiter;

use RateLimiter\Storage\StorageInterface;
use RateLimiter\Strategy\StrategyInterface;

class RateLimiter {
    private $storage;
    private $strategy;

    public function __construct(StorageInterface $storage, StrategyInterface $strategy) {
        $this->storage = $storage;
        $this->strategy = $strategy;
    }

    public function isRateLimited($key, $isGlobal) {
        if ($isGlobal) {
            $key = get_class($this->strategy)."globalRateLimit";
        } else {
            $key = get_class($this->strategy).$key;
        }
        $isLimit = false;

        //限速器逻辑存在并发读写，加排他锁，也可采用其他方案，暂用文件锁
        $fp = fopen("/tmp/".md5($key).".ratelimit.lock", "w+");
        if ($fp) {
            // 尝试获取锁
            if (flock($fp, LOCK_EX)) {
                // 成功获得锁
//                echo "Got lock\n";
                // 执行需要锁的操作
                $isLimit = $this->strategy->isRateLimited($key);
                // 释放锁
                flock($fp, LOCK_UN);
            } else {
                // 未能获得锁
//                echo "Failed to get lock\n";
            }
            fclose($fp);
        }

        return $isLimit;
    }
}
