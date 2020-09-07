# PHP + Redis 实现 Bloom 过滤器

## 使用者
### 1、安装
```
composer require m-sql/boom
```

### 2、测试
```
<?php
include './vendor/autoload.php';

/**
 * 缓存key不存在,过滤器
 *
 * 该布隆过滤器总位数为2^32位, 判断条数为2^30条. hash函数最优为3个.(能够容忍最多的hash函数个数)
 * 使用的三个hash函数为
 * BKDR, SDBM, JSHash 等*/
class FilterNotKey extends \Bloom\FilterRedis
{
    /**
     * 不存在key,过滤器
     * @var string
     */
    protected $bucket = 'not:key';

    /**
     * @var array $hashFunction 哈希算法
     */
    protected $hashFunction = ['BKDRHash', 'SDBMHash', 'JSHash'];

}

/**
 * Redis
 */
$config    = [
    'host'   => '127.0.0.1'
    , 'port' => 6379,
];
$filterObj = new FilterNotKey($config, 0);

/**
 * 开始测试*/
$exit = $filterObj->exists('10');
var_dump($exit);
if (!$exit) {
    $add = $filterObj->add('10');
    var_dump(json_encode($add));
}

$exit = $filterObj->exists('130');
var_dump($exit);
if (!$exit) {
    $add = $filterObj->add('130');
    var_dump(json_encode($add));
}

$exit = $filterObj->exists('33');
var_dump($exit);

echo "----[ok]----" . "\n";


```


## 研发者
### 1、安装
```
composer i
```

### 2、测试
```
php test.php
```
