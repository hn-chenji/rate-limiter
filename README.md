# API限流器
## 该限流器采用php技术开发，支持以下特性
### 存储方式
* 文件存储
* redis存储

### 流控策略
* 固定窗口策略
* 滑动窗口策略
* 漏桶策略

## 项目结构
```
rate-limiter/
├── src/
│   ├── RateLimiter.php                     限流器类
│   ├── StorageFactory.php                  存储工厂类
│   ├── StrategyFactory.php                 策略工厂类
│   ├── Storage/                           
│   │   ├── StorageInterface.php            存储接口
│   │   ├── FileStorage.php                 文件存储实现类
│   │   └── RedisStorage.php                redis存储实现类
│   └── Strategy/
│       ├── StrategyInterface.php           策略接口
│       ├── FixedWindowStrategy.php         固定窗口策略类
│       ├── SlidingWindowStrategy.php       滑动窗口策略类
│       └── LeakyBucketStrategy.php         漏桶策略类
├── composer.json
└── README.md
```

## 使用方法
1. 添加依赖  
```
composer require cjphp/rate-limiter:~1.0
```
2. 在项目需要限流的地方初始化 RateLimiter 类，并调用 isRateLimited 方法，示例如下
```
    public function needRateLimitFunction()
    {
        ...
        
        $key = 'user_123'; //key 为特定对象，此处假设用户id
        //以下可以写入配置文件 动态读取
        $storeType = "file";  //可选：file|redis
        $strategyType = "fixed_window"; //可选：fixed_window|sliding_window|leaky_bucket
        $limit = 10; // 每60秒 10 次请求
        $window = 60; // 60 秒
        $rate = 1; // 每秒漏5滴
        $capacity = 3; // 桶容量10
        $isGlobal = true; //是否全局限流
        $params = ["limit"=>$limit, "window"=> $window, "capacity"=>$capacity, "rate"=>$rate];

        $ext = match ($storeType) {
            'file' => '文件存储路径...',
            'redis' => redis实例,
        };
        $storage = StorageFactory::create($storeType, $ext);
        $strategy = StrategyFactory::create($strategyType, $storage, $params);

        $rateLimiter = new RateLimiter($storage, $strategy);

        if ($rateLimiter->isRateLimited($key, $isGlobal)) {
            echo "不好意思，您被流控了";
        } else {
            echo "允许通行.";
        }
        
        ...
    }
```

