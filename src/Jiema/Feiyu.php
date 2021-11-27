<?php

namespace Ehua\Jiema;

use GuzzleHttp\Client;

/**
 * 飞鱼接码操作类
 * Class Feiyu
 * @package Jiema
 */
class Feiyu
{

    /**
     *飞鱼接码API
     * API调用一定要加延时过快访问会被锁号.
     * 统一返回 URL 编码字符，返回内容 请用 URL解码 可正常显示
     */
    public $token;

    public function __construct($config)
    {
        $url = "http://sms168.xyz:82/api/yonghu_login";
        $data = [
            'username' => $config['name'],
            'password' => $config['pwd'],
            'type' => 1,
        ];
        $token = $this->post($url, $data);
        $this->token = $token[1];
    }

    /**
     * 获取账户余额
     * @return bool|string
     */
    public function getmoney()
    {
        $url = "http://sms168.xyz:889/api/xhqyhzb";
        $data = [
            'lx' => '3',
            'token' => $this->token,
        ];
        $res = $this->post($url, $data);
        return $res;
    }

    /**
     * 获取手机号
     * @param $xmid     项目id
     * @param $xzgj     国家       (支持:不限,中国,泰国,俄罗斯,英国,肯尼亚,巴拿马,越南,乌干达,马来西亚,美国,菲律宾,巴西,缅甸,柬埔寨,印度尼西亚)
     * @param $xzyys    运营商      (支持:不限,移动,联通,电信)
     * @param $xzsf     省份      (支持:不限,北京,上海,天津,山东,福建,安徽,宁夏,湖北,河北,辽宁,湖南,四川,山西,陕西,江苏,内蒙,辽宁,吉林,甘肃,江西,河南,云南,广西,重庆,西藏,海南,广东,浙江,贵州,黑龙江,新疆,澳门,香港)
     * @param $hmlx     卡类型     (支持:不限,虚拟,实卡)
     * @param $glhmd    过滤号码     (如果取号需要过滤已拉黑过的手机号 提交参数glhmd=1 不过滤glhmd=)
     * @param $qhsl     取号数量     (提交数字)
     * @param $dcjs     号码使用次数   (提交数字)
     * @param $kfz      开发者
     * @return bool|string
     */
    public function getphone($xmid, $xzgj, $xzyys, $xzsf, $hmlx, $glhmd, $qhsl, $dcjs, $kfz)
    {

        $url = "http://sms168.xyz:84/api/shouduanxin_zaixianhaoma_plpt";
        $data = [
            'xmid' => $xmid,
            'xzgj' => $xzgj,
            'xzyys' => $xzyys,
            'xzsf' => $xzsf,
            'hmlx' => $hmlx,
            'glhmd' => $glhmd,
            'qhsl' => $qhsl,
            'dcjs' => $dcjs,
            'kfz' => $kfz,
            'token' => $this->token,
        ];
        $res = $this->post($url, $data);
        return $res;
    }

    /**
     * 获取验证码
     * @param $xmid 项目id
     * @param $sjhm 手机号码
     * @return bool|string
     */
    public function getsms($xmid, $sjhm)
    {

        $url = "http://sms168.xyz:85/api/shouduanxin_quma";
        $data = [
            'xmid' => $xmid,
            'sjhm' => $sjhm,
            'token' => $this->token,
        ];
        $res = $this->post($url, $data);
        return $res;
    }

    /**
     * 释放手机号
     * @param $xmid 项目id
     * @param $sjhm 手机号码
     * @return bool|string
     */
    public function getdel($xmid, $sjhm)
    {

        $url = "http://sms168.xyz:86/api/shouduanxin_shifang";
        $data = [
            'xmid' => $xmid,
            'sjhm' => $sjhm,
            'token' => $this->token,
        ];
        $res = $this->post($url, $data);
        return $res;
    }

    /**
     * 拉黑号码
     * @param $xmid 项目id
     * @param $sjhm 手机号码
     * @return bool|string
     */
    public function getdhei($xmid, $sjhm)
    {

        $url = "http://sms168.xyz:86/api/shouduanxin_lahei";
        $data = [
            'xmid' => $xmid,
            'sjhm' => $sjhm,
            'token' => $this->token,
        ];
        $res = $this->post($url, $data);
        return $res;
    }


    private function post($url, $postdata)
    {
        $GUZZ = new Client();

        $data = $GUZZ->post($url, [
            'form_params' => $postdata,
        ])->getBody();
        $data = (string)$data;
        $data = urldecode($data);
        $data = explode('|', $data);
        return $data;
    }


    /**
     * 调用实例
    $xmid='01836';

    $hei='';
    $config = [
    'name' => '',
    'pwd' => '',
    ];
    $guojia=[ '泰国','俄罗斯','英国','肯尼亚','巴拿马','越南','乌干达','马来西亚','美国','菲律宾','巴西','缅甸','柬埔寨','印度尼西亚'];
    $guojia= $guojia[rand(0,count($guojia)-1)];

    $jiema = new \Jiema\Feiyu($config);
    $res = $jiema->getphone($xmid,$guojia,'不限','不限','不限',$hei,2,1,'123');

     */



}