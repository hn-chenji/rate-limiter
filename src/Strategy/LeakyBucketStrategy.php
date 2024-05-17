<?php

namespace RateLimiter\Strategy;

use RateLimiter\Storage\StorageInterface;

/**
 * 漏桶策略
 */
class LeakyBucketStrategy implements StrategyInterface
{
    private $storage;
    private $rate; // 流出的速率
    private $capacity; // 桶的容量

    public function __construct(StorageInterface $storage, int $rate, int $capacity)
    {
        $this->storage = $storage;
        $this->rate = $rate;
        $this->capacity = $capacity;
    }

    public function isRateLimited($key){
        $now = time();
        $data = $this->storage->get($key);
        if ($data === null) {
            $count = 0;
            $lastTime = $now;
        } else {
            $count = $data['count'];
            $lastTime = $data['lastTime'];
        }

        //计算剩余水量
        $remainCount = max(0,$count - ($now - $lastTime) * $this->rate);
        if($remainCount < $this->capacity){
            // 桶未满加水
            $data['count'] = $remainCount + 1;
            $data['lastTime'] = $now;
            $this->storage->set($key, $data);
            return false;
        }else{
            // 水满，拒绝加水
            return true;
        }
    }


}
