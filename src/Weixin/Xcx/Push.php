<?php


namespace Ehua\Weixin\Xcx;


class Push extends Common
{
    /*
      发送小程序推送信息
      **/
    public function init($openid, $data)
    {
        $template_id='EFm5A5lqDubW8QmTTQul3mw3jMcOp2oEciMQ9MjlIzA';

        //access_token 是用来获取accesstoken
        //openid 已经存到用户表里面
        //模板的id：EFm5A5lqDubW8QmTTQul3mw3jMcOp2oEciMQ9MjlIzA
        //模板编号：32361

        //订阅消息的模板消息
        $msgObj = [
            "phone_number5" => [
                "value" => $data['phone_number5']//电话
            ],
            "time1" => [
                "value" => $data['time1']//时间
            ],
            "thing2" => [
                "value" => $data['thing2']//地址
            ],
            "thing7" => [
                "value" => $data['thing7']//名称
            ]
        ];
        //请求的data数据
        $msgData = [
            'touser' => $openid,
            'template_id' => $template_id,
//            'miniprogram_state' => 'developer',
            'page' => '/pages/task/task',
            'data' => $msgObj
        ];

        return $this->sendMessage($msgData);
    }

    private function sendMessage($data = [])
    {
        $res = [
            'appid' => $this->AppID,
            'appsecret' => $this->AppSecret,
        ];
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $res['appid'] . '&secret=' . $res['appsecret'];
        //json_encode 	对变量进行 JSON 编码     file_get_contents() 把整个文件读入一个字符串中。
        $res = json_decode(file_get_contents($url), true);

        $access_token = $res['access_token'];

        //请求url
        $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=' . $access_token;

        return $this->curlPost($url, json_encode($data));
    }


//发送post请求 小程序发送一次性订阅消息
    private function curlPost($url, $data)
    {
        $ch = curl_init();
        $params[CURLOPT_URL] = $url;    //请求url地址
        $params[CURLOPT_HEADER] = FALSE; //是否返回响应头信息
        $params[CURLOPT_SSL_VERIFYPEER] = false;
        $params[CURLOPT_SSL_VERIFYHOST] = false;
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_POSTFIELDS] = $data;
        curl_setopt_array($ch, $params); //传入curl参数
        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
        return $content;
    }
}