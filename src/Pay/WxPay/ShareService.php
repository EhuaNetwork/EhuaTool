<?php


namespace Ehua\WxPay;


class ShareService
{
    protected $appid;
    protected $appKey;
    public $token = null;

    public function __construct($appid, $appKey)
    {
        $this->appid = $appid;
        $this->appKey = $appKey;
        $this->token = $this->wx_get_token();
    }

    //获取微信公从号access_token
    public function wx_get_token()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->appKey;
        $res = self::curlGet($url);
        $res = json_decode($res, true);
        if ($res['errmsg']) {
            echo json_encode($res);
            exit();
        }
        //这里应该把access_token缓存起来，有效期是7200s
        return $res['access_token'];
    }

    //获取微信公从号ticket
    public function wx_get_jsapi_ticket()
    {
        $url = sprintf("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi", $this->token);
        $res = self::curlGet($url);
        $res = json_decode($res, true);
        //这里应该把ticket缓存起来，有效期是7200s
        return $res['ticket'];
    }

    public function getShareConfig($url)
    {
//        $wx = array();
//        //生成签名的时间戳
//        $wx['timestamp'] = time();
//        //生成签名的随机串
//        $wx['noncestr'] = uniqid();
//        //jsapi_ticket是公众号用于调用微信JS接口的临时票据。正常情况下，jsapi_ticket的有效期为7200秒，通过access_token来获取。
//        $wx['jsapi_ticket'] = $this->wx_get_jsapi_ticket();
//        //分享的地址
//        $wx['url'] = $url;
//        $string = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s", $wx['jsapi_ticket'], $wx['noncestr'], $wx['timestamp'], $wx['url']);
//        //生成签名
//        $wx['signature'] = sha1($string);
//


        $jsapiTicket = $this->wx_get_jsapi_ticket();
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
//        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
//        var_dump($jsapiTicket);
//        var_dump($nonceStr);
//        var_dump($timestamp);
//        var_dump($url);
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => config('appid'),
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public static function curlGet($url = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}