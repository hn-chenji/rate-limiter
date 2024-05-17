<?php

namespace RateLimiter\Strategy;

use RateLimiter\Storage\StorageInterface;

/**
 * 固定窗口策略
 */
class FixedWindowStrategy implements StrategyInterface {
    private $storage;
    private $limit; //限制次数
    private $window; //窗口大小

    public function __construct(StorageInterface $storage, int $limit, int $window) {
        $this->storage = $storage;
        $this->limit = $limit;
        $this->window = $window;
    }

    /**
     * @param $key 指定键 如userId
     * @param $limit 时间窗口内限制次数
     * @param $window 时间窗口 单位秒
     * @return bool true:限行 false:放行
     */
    public function isRateLimited($key) {
        $data = $this->storage->get($key);
        $expiry = time() + $this->window;
        if ($data === null) {
            $count = 0;
        } else {
            if ($data['expiry'] > time()) {
                $count = $data['count'];
                $expiry = $data['expiry'];
            } else {
                //过期重置窗口
                $count = 0;
                $this->storage->delete($key);
            }
        }

        if ($count >= $this->limit) {
            return true;
        }
        $value['count'] = $count+1;
        $value['expiry'] = $expiry;

        $this->storage->set($key, $value);
        return false;
    }
}
