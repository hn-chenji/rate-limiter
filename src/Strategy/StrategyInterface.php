<?php

namespace RateLimiter\Strategy;

interface StrategyInterface {
    public function isRateLimited($key);
}
