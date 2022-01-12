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
 * Post请求
 * Class Post
 * @package Ehua\Bt\Curl
 */
class Post extends BasicCurl
{
    /**
     * 发送Post请求
     * @param string $url 网址
     * @param array $post_data 参数
     * @param bool $is_json 是否返回Json格式
     * @param string $headers
     * @return array|bool|mixed|string
     * @throws CurlException
     */
    public function http(string $url, array $post_data = [], bool $is_json = false, string $headers = 'application/json;charset=utf-8')
    {
        if (!extension_loaded("curl")) throw new CurlException('请开启curl模块！', E_USER_DEPRECATED);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: ' . $headers));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        $content = curl_exec($ch);
        curl_close($ch);
        if (empty($is_json)) return $content;
        try {
            return json_decode($content, true);
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * 发送Xml数据
     * @param string $url
     * @param string $xmlData
     * @param string $headers
     * @param int $second 设置超时
     * @return string
     * @throws CurlException
     */
    public function xml(string $url, string $xmlData = '', string $headers = 'application/json;charset=utf-8', $second = 60)
    {
        //首先检测是否支持curl
        if (!extension_loaded("curl")) throw new CurlException('请开启curl模块！', E_USER_DEPRECATED);
        //初始一个curl会话
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        set_time_limit(0);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: ' . $headers));
        }
        //运行curl
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 上传图片
     * @param string $url
     * @param array $post_data
     * @param string $headers
     * @param bool $userCert
     * @param int $timeout
     * @param $sslCertPath
     * @param $sslKeyPath
     * @return false|string
     * @throws CurlException
     */
    public function file(string $url, $post_data = [],string $headers = '',bool $userCert = false,int $timeout = 30, $sslCertPath, $sslKeyPath)
    {
        //首先检测是否支持curl
        if (!extension_loaded("curl")) throw new CurlException('请开启curl模块！', E_USER_DEPRECATED);
        //初始一个curl会话
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($xmlData)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELD, $post_data);
        }
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (empty($userCert)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $sslCertPath);
            curl_setopt($ch, CURLOPT_SSLKEY, $sslKeyPath);
        } else {
            if (substr($url, 0, 5) == 'https') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名
            }
        }
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: ' . $headers));
        }
        curl_setopt($ch, CURLOPT_HEADER, true);    // 是否需要响应 header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);    // 获得响应结果里的：头大小
        $response_header = substr($output, 0, $header_size);    // 根据头大小去获取头信息内容
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);    // 获取响应状态码
        $response_body = substr($output, $header_size);
        $error = curl_error($ch);
        curl_close($ch);
        $data = [
            'request_url' => $url,
            'request_body' => serialize($post_data),
            'request_header' => serialize($headers),
            'response_http_code' => $http_code,
            'response_body' => serialize($response_body),
            'response_header' => serialize($response_header),
        ];
        return $response_body;
    }
}
