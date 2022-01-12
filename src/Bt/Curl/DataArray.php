<?php

// +----------------------------------------------------------------------
// | 网络请求助手
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 [ https://www.Ehua.net ]
// +----------------------------------------------------------------------
// | 官方网站: https://gitee.com/liguangchun/curl
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 仓库地址 ：https://gitee.com/liguangchun/curl
// | github 仓库地址 ：https://github.com/GC0202/curl
// | Packagist 地址 ：https://packagist.org/packages/liguangchun/curl
// +----------------------------------------------------------------------

namespace Ehua\Bt\Curl;

use ArrayAccess;

/**
 * 配置
 * Class DataArray
 * @package Ehua\Ip
 */
class DataArray implements ArrayAccess
{
    /**
     * 当前配置值
     * @var array
     */
    private $config = [];

    /**
     * DataArray constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->config = $options;
    }

    /**
     * 设置配置项值
     * @param $offset
     * @param $value
     */
    public function set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    /**
     * 获取配置项参数
     * @param null $offset
     * @return mixed|void
     */
    public function get($offset = null)
    {
        return $this->offsetGet($offset);
    }

    /**
     * 合并数据到对象
     * @param array $data 需要合并的数据
     * @param bool $append 是否追加数据
     * @return array
     */
    public function merge(array $data, $append = false)
    {
        if ($append) return $this->config = array_merge($this->config, $data);
        return array_merge($this->config, $data);
    }

    /**
     * 设置配置项值
     * @param string $offset
     * @param string|array|null|integer $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) $this->config[] = $value;
        $this->config[$offset] = $value;
    }

    /**
     * 判断配置Key是否存在
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    /**
     * 清理配置项
     * @param string|null $offset
     */
    public function offsetUnset($offset = null)
    {
        if (is_null($offset)) $this->config = [];
        unset($this->config[$offset]);
    }

    /**
     * 获取配置项参数
     * @param string|null $offset
     * @return array|string|null
     */
    public function offsetGet($offset = null)
    {
        if (is_null($offset)) return $this->config;
        return isset($this->config[$offset]) ? $this->config[$offset] : null;
    }
}
