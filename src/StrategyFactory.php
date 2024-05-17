<?php

namespace RateLimiter;

use RateLimiter\Storage\StorageInterface;
use RateLimiter\Strategy\FixedWindowStrategy;
use RateLimiter\Strategy\LeakyBucketStrategy;
use RateLimiter\Strategy\SlidingWindowStrategy;
use RateLimiter\Strategy\StrategyInterface;
use RuntimeException;

abstract class StrategyFactory
{
    public static function create(string $strategy, StorageInterface $storage, $params): StrategyInterface
    {
        switch ($strategy) {
            case 'fixed_window' :
                list("limit" => $limit, "window" => $window) = $params;
                $strategyInstance = new FixedWindowStrategy($storage, $limit, $window);
                break;
            case 'sliding_window' :
                list("limit" => $limit, "window" => $window) = $params;
                $strategyInstance = new SlidingWindowStrategy($storage, $limit, $window);
                break;
            case 'leaky_bucket' :
                list("capacity" => $capacity, "rate" => $rate) = $params;
                $strategyInstance = new LeakyBucketStrategy($storage, $rate, $capacity);
                break;
            default:
                throw new RuntimeException('未匹配到相应策略');
        };

        return $strategyInstance;
    }

}