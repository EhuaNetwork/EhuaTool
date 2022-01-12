<?php

// +----------------------------------------------------------------------
// | 宝塔PHP扩展包
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 [ https://www.Ehua.net ]
// +----------------------------------------------------------------------
// | 官方网站: https://gitee.com/liguangchun/bt
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 仓库地址 ：https://gitee.com/liguangchun/bt
// | github 仓库地址 ：https://github.com/GC0202/bt
// | Packagist 地址 ：https://packagist.org/packages/liguangchun/bt
// +----------------------------------------------------------------------

namespace Ehua\Bt;

use Ehua\Bt\Curl\CurlException;

/**
 * 设置
 * Class Setup
 * @package Ehua\Bt
 */
class Setup extends BaseBt
{
    /**
     * 获取消息通道
     * @return mixed
     * @throws CurlException
     */
    public function getNews()
    {
        $url = '/config?action=get_settings';
        //请求面板接口
        return $this->HttpPostCookie($url, []);
    }
}
