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
 * 判断
 * Class Judge
 * @package Ehua\Bt\Curl
 */
class Judge extends BasicCurl
{
    /**
     * 判断是否为GET方式
     * @return bool
     */
    public function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    /**
     * 判断是否为POST方式
     * @return bool
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
    }

    /**
     * 判断是否为PUT方式
     * @return boolean
     */
    public function isPut()
    {
        return $_SERVER['REQUEST_METHOD'] == 'PUT' ? true : false;
    }

    /**
     * 判断是否为DELETE方式
     * @return boolean
     */
    public function isDelete()
    {
        return $_SERVER['REQUEST_METHOD'] == 'DETELE' ? true : false;
    }
}
