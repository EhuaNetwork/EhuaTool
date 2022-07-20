<?php


namespace Ehua\Weixin\Gzh;


use think\Cache;

class Push extends Common
{
    /**
     * 发送模板消息
     */
//    public $appid = 'wx261336e683a2e428';
//    public $appsecret = '749b2c9992e90c40f41f00bdf4005bdb';

    public $tenpalate_id;

    public function __construct($cpnfig)
    {
        parent::__construct($cpnfig);
        $this->tenpalate_id = 'w_B5FwFVLx3ADQbegCiZXJXxa4n-woxrbvUcsm5_Ckg';
    }

    public function init($openid, $data)
    {
        //获取access_token

        if (Cache::get('access_token')) {
            $access_token2 = Cache::get('access_token');
        } else {
            $appid = $this->AppID;
            $secret = $this->AppSecret;
            $json_token = $this->curl_post("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret . "");


            $access_token1 = json_decode($json_token, true);
            $access_token2 = $access_token1['access_token'];
            Cache::set('access_token', $access_token2, 7200);
        }
        //模板消息
        $json_template = $this->json_tempalte($openid, $data);

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token2;

        $res = $this->curl_post($url, urldecode($json_template));
        $res = explode(',', $res);
        if ($res[0] == 0) {
            return '发送成功';
        } else {
            return '发送失败';
        }
    }

    /**
     * 将模板消息json格式化
     */
    public function json_tempalte($openid, $data)
    {
        //模板消息
        $tenpalate_id = $this->tenpalate_id;
        $template = array(
            'touser' => "$openid", //用户openid
            'template_id' => "$tenpalate_id", //在公众号下配置的模板id
//            'url' => "$url", //点击模板消息会跳转的链接
            'topcolor' => "#7B68EE",
//            "miniprogram" => [
//                "appid" => "wxc9190f57922113ef",//跳转的小程序id
//                "pagepath" => "pages/task/task"
//            ],
            'data' => array(
                'first' => array('value' => urlencode($data['first']), 'color' => "#FF0000"),
                'keyword1' => array('value' => urlencode($data['thing7']), 'color' => '#FF0000'), //keyword需要与配置的模板消息对应
                'keyword2' => array('value' => urlencode($data['thing2']), 'color' => '#FF0000'),
                'remark' => array('value' => urlencode($data['time1']), 'color' => '#FF0000'),)
        );
        $json_template = json_encode($template);
        return $json_template;
    }

    /**
     * @param $url
     * @param array $data
     * @return mixed
     * curl请求
     */
    function curl_post($url, $data = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // POST数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}