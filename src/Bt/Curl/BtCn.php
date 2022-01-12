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

use Exception;

/**
 * 宝塔专属请求接口
 * Class BtCn
 * @package Ehua\Bt\Curl
 */
class BtCn extends BasicCurl
{
    /**
     * 发起POST请求
     * @param String $url 网址
     * @param array $data 数据
     * @param string $cookie 认证内容
     * @param int $timeout 超时，默认60s
     * @param bool $is_json 是否返回Json格式
     * @return bool|mixed|string
     * @throws CurlException
     */
    public function httpPost(string $url = '', array $data = [], string $cookie = '', int $timeout = 60, bool $is_json = false)
    {
        if (empty($cookie)) throw new CurlException('请检查cookie内容');
        if (empty($this->config->get('bt_panel'))) throw new CurlException('请配置宝塔网址, 【bt_panel】');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config->get('bt_panel') . $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge($this->getKeyData(), $data));
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        if (empty($is_json)) return $output;
        try {
            return json_decode($output, true);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 构造带有签名的关联数组
     * @return array
     * @throws CurlException
     */
    private function getKeyData()
    {
        if (empty($this->config->get('bt_key'))) throw new CurlException('请配置宝塔密钥，【bt_key】');
        $time = time();
        return array(
            'request_token' => md5($time . '' . md5($this->config->get('bt_key'))),
            'request_time' => $time
        );
    }
}
