<?php
namespace Bloom;

/**
 * Class FilterHash
 * @package Bloom
 * @User    : lidi
 * @Email   : lucklidi@126.com
 * @Date    : 2020-09-07
 */
class FilterHash
{
    /**
     * ustin Sobel编写的按位散列函数
     *
     * @param string $string
     * @param null $len
     * @return int
     * @User : lidi
     * @Email: lucklidi@126.com
     * @Date : 2020-09-07
     */
    public function JSHash($string, $len = null)
    {
        $hash = 1315423911;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash ^= (($hash << 5) + ord($string[$i]) + ($hash >> 2));
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * 基于AT＆T贝尔实验室的Peter J. Weinberge《Aho Sethi和Ulman编写的“编译器（原理，技术和工具）》
     *
     * @param string $string
     * @param null $len
     * @return int
     * @User : lidi
     * @Email: lucklidi@126.com
     * @Date : 2020-09-07
     */
    public function PJWHash($string, $len = null)
    {
        $bitsInUnsignedInt = 4 * 8; //（unsigned int）（sizeof（unsigned int）* 8）;
        $threeQuarters     = ($bitsInUnsignedInt * 3) / 4;
        $oneEighth         = $bitsInUnsignedInt / 8;
        $highBits          = 0xFFFFFFFF << (int)($bitsInUnsignedInt - $oneEighth);
        $hash              = 0;
        $test              = 0;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = ($hash << (int)($oneEighth)) + ord($string[$i]);
        }
        $test = $hash & $highBits;
        if ($test != 0) {
            $hash = (($hash ^ ($test >> (int)($threeQuarters))) & (~$highBits));
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * 针对32位处理器进行了调整,基于UNIX的系统上的widley使用哈希函数
     *
     * @param string $string
     * @param null $len
     * @return int
     * @User : lidi
     * @Email: lucklidi@126.com
     * @Date : 2020-09-07
     */
    public function ELFHash($string, $len = null)
    {
        $hash = 0;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = ($hash << 4) + ord($string[$i]);
            $x    = $hash & 0xF0000000;
            if ($x != 0) {
                $hash ^= ($x >> 24);
            }
            $hash &= ~$x;
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * Brian Kernighan和Dennis Ritchie的书“The C Programming Language”
     *
     * @param string $string
     * @param null $len
     * @return int
     * @User : lidi
     * @Email: lucklidi@126.com
     * @Date : 2020-09-07
     */
    public function BKDRHash($string, $len = null)
    {
        $seed = 131;  # 31 131 1313 13131 131313 etc..
        $hash = 0;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = (int)(($hash * $seed) + ord($string[$i]));
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * 开源SDBM项目中使用的首选算法,适用于数据集中元素的MSB存在高差异的情况
     *
     * @param string $string
     * @param null $len
     * @return int
     * @User : lidi
     * @Email: lucklidi@126.com
     * @Date : 2020-09-07
     */
    public function SDBMHash($string, $len = null)
    {
        $hash = 0;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = (int)(ord($string[$i]) + ($hash << 6) + ($hash << 16) - $hash);
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * Daniel J. Bernstein教授在usenet新闻组comp.lang.c演示,最有效的哈希函数之一
     *
     * @param string $string
     * @param null $len
     * @return int
     * @User : lidi
     * @Email: lucklidi@126.com
     * @Date : 2020-09-07
     */
    public function DJBHash($string, $len = null)
    {
        $hash = 5381;
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = (int)(($hash << 5) + $hash) + ord($string[$i]);
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * Donald E. Knuth在“计算机编程艺术第3卷”中提出的算法，主题是排序和搜索第6.4章
     *
     * @param string $string
     * @param null $len
     * @return int
     * @User : lidi
     * @Email: lucklidi@126.com
     * @Date : 2020-09-07
     */
    public function DEKHash($string, $len = null)
    {
        $len || $len = strlen($string);
        $hash = $len;
        for ($i = 0; $i < $len; $i++) {
            $hash = (($hash << 5) ^ ($hash >> 27)) ^ ord($string[$i]);
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }

    /**
     * 参考 http://www.isthe.com/chongo/tech/comp/fnv/
     *
     * @param string $string
     * @param null $len
     * @return int
     * @User : lidi
     * @Email: lucklidi@126.com
     * @Date : 2020-09-07
     */
    public function FNVHash($string, $len = null)
    {
        $prime = 16777619; //32位的prime 2^24 + 2^8 + 0x93 = 16777619
        $hash  = 2166136261; //32位的offset
        $len || $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $hash = (int)($hash * $prime) % 0xFFFFFFFF;
            $hash ^= ord($string[$i]);
        }
        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }
}