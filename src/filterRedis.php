<?php
namespace Bloom;

/**
 * Redis实现的布隆过滤器
 *
 * Class FilterRedis
 * @package Bloom
 * @User    : lidi
 * @Email   : lucklidi@126.com
 * @Date    : 2020-09-07
 */
abstract class FilterRedis
{
    /**
     * 定义布隆过滤器的bucket名字*/
    protected $bucket;

    /**
     * @var array $hashFunction 采用hash的方法 */
    protected $hashFunction;

    /**
     * FilterRedis constructor.
     * @param array $config [ 'host'=>'IP地址', 'port'=>'端口']
     * @param int   $id
     * @throws \Exception
     */
    public function __construct($config=[], $id=0)
    {
        if (!$this->bucket || !$this->hashFunction) {
            throw new \Exception("Please construct bucket and hashFunction", 1);
        }
        $this->Hash  = new FilterHash();
        /**
         * PHP-Redis*/
        $redis = new \Redis();
        $redis->connect($config['host'], $config['port']);
        if (isset($config['auth'])) {
            $redis->auth($config['auth']);
        }
        if ($id) {
            $redis->select($id);
        }
        $this->Redis = $redis;
    }

    /**
     * 添加到集合中
     *
     * @param $string
     * @return mixed
     * @User : lidi
     * @Email: lucklidi@126.com
     * @Date : 2020-09-07
     */
    public function add($string)
    {
        $pipe = $this->Redis->multi();
        foreach ($this->hashFunction as $function) {
            $hash = $this->Hash->$function($string);
            $pipe->setBit($this->bucket, $hash, 1);
        }
        return $pipe->exec();
    }

    /**
     * 查询是否存在（存在的有一定几率会误判, 不存在的一定不存在）
     *
     * @param $string
     * @return bool
     * @User : lidi
     * @Email: lucklidi@126.com
     * @Date : 2020-09-07
     */
    public function exists($string)
    {
        $pipe = $this->Redis->multi();
        $len  = strlen($string);
        foreach ($this->hashFunction as $function) {
            $hash = $this->Hash->$function($string, $len);
            $pipe = $pipe->getBit($this->bucket, $hash);
        }
        $res = $pipe->exec();
        foreach ($res as $bit) {
            if ($bit == 0) {
                return false;
            }
        }
        return true;
    }

}