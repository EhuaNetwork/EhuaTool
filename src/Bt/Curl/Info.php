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
 * 信息
 * Class Info
 * @package Ehua\Bt\Curl
 */
class Info extends BasicCurl
{
    /**
     * 获取域名地址
     * @return string
     */
    public function getWebsiteAddress()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return $http_type . $_SERVER['HTTP_HOST'] . "/";
    }

    /**
     * 返回成功
     * @param array $data
     * @param string $msg
     * @param int $code
     */
    public function retJsonSuccess(array $data = [], string $msg = 'success', int $code = 0)
    {
        date_default_timezone_set('Asia/Shanghai');
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(['code' => $code, 'msg' => $msg, 'time' => time(), 'data' => $data]);
        exit;
    }

    /**
     * 返回失败
     * @param string $msg
     * @param int $code
     * @param array $data
     */
    public function retJsonError(string $msg = 'error', int $code = 1, array $data = [])
    {
        date_default_timezone_set('Asia/Shanghai');
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(['code' => $code, 'msg' => $msg, 'time' => time(), 'data' => $data]);
        exit;
    }
}
