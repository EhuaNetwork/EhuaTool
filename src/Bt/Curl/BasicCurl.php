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

/**
 * 中间件
 * Class BasicIp
 * @package Ehua\Ip
 */
class BasicCurl
{
    /**
     * 定义当前版本
     */
    const VERSION = '1.0.10';

    /**
     * 配置
     * @var
     */
    public $config;

    /**
     * Base constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->config = new DataArray($options);
    }
}
