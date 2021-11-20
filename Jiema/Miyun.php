<?php

namespace Jiema;

use GuzzleHttp\Client;

class Miyun
{


    /**

    //项目id
    $xmid = '14016';

    $hei = '';
    //米云账号
    $config = [
    'name' => 'MY.231220714',
    'pwd' => '150638',
    ];


    $glhmd = '162,165,163,166,167,169,171,170,172,175,173,177,179';
    $jiema = new \Jiema\Miyun($config);
    sleep(10);

    $res = $jiema->getphone($xmid, '', '', 0, $glhmd, '', 'ehua999');
    var_dump($res);
    sleep(10);
    $res = $jiema->getdel($xmid,$res['mobile']);

     */



    /**
     *飞鱼接码API  http://www.miyun.pro/api.html
     * API调用一定要加延时过快访问会被锁号.
     * 统一返回 URL 编码字符，返回内容 请用 URL解码 可正常显示
     */

    public $token;

    public $domain = "http://api.miyun.pro/";

    public function __construct($config)
    {
        $url = $this->domain . "api/login";
        $data = [
            'apiName' => $config['name'],
            'password' => $config['pwd'],
        ];
        $token = $this->post($url, $data);
        $this->token = $token['token'];
    }

    /**
     * @return bool|string
     */
    public function getmoney()
    {
        $url = $this->domain . "api/get_myinfo";
        $data = [
            'token' => $this->token,
        ];
        $res = $this->post($url, $data);
        return $res;
    }

    /**
     * @param $xmid         项目编号
     * @param $scope        指定号段查询 (譬如:137开头的号段或者1371开头的号段),最多支持20个号段。用逗号分隔 比如150,1501,1502
     * @param $xzsf         归属地选择 例如 湖北 甘肃 不需要带省、市字样
     * @param $hmlx         卡类型 (0=默认 4=实卡 5=虚卡) 可为空,请传数字
     * @param $glhmd        排除号段最长支持4位且可以支持多个,最多支持20个号段。用逗号分隔 比如184,1841
     * @param $qhsl         指定取号的话,这里填要取的手机号
     * @param $kfz          开发者返现账号（请填写登录的用户名）
     * @return false|string[]
     */
    public function getphone($xmid, $scope,  $xzsf, $hmlx, $glhmd, $qhsl, $kfz)
    {

        $url = $this->domain . "api/get_mobile";

        $data = [
            'project_id' => $xmid,
            'operator' => $hmlx,
            'phone_num' => $qhsl,
            'scope_black' => $glhmd,
            'address' => $xzsf,
            'api_id' => $kfz,
            'token' => $this->token,
            'scope' => $scope,

        ];
        $res = $this->post($url, $data);
//     $res->minute: 为分钟接码数量。切勿<0，否则会封号处理！
        return $res;
    }


    /**
     * @param $xmid 项目id
     * @param $sjhm 手机号码
     * @return bool|string
     */
    public function getsms($xmid, $sjhm)
    {

        $url = $this->domain . "api/get_message";

        $data = [
            'project_id' => $xmid,
            'phone_num' => $sjhm,
            'token' => $this->token,
        ];
        $res = $this->post($url, $data);
        return $res;
    }

    /**
     * @param $xmid 项目id
     * @param $sjhm 手机号码
     * @return bool|string
     */
    public function getdel($xmid, $sjhm)
    {

        $url = $this->domain . "api/free_mobile";
        $data = [
            'project_id' => $xmid,
            'phone_num' => $sjhm,
            'token' => $this->token,
        ];
        $res = $this->post($url, $data);
        return $res;
    }

    /**
     * @param $xmid 项目id
     * @param $sjhm 手机号码
     * @return bool|string
     */
    public function getdhei($xmid, $sjhm)
    {

        $url = $this->domain . "api/add_blacklist";
        $data = [
            'project_id' => $xmid,
            'phone_num' => $sjhm,
            'token' => $this->token,
        ];
        $res = $this->post($url, $data);
        return $res;
    }




    // $url 是请求的链接
// $postdata 是传输的数据，数组格式
    function post($url, $postdata)
    {
        $GUZZ = new Client();

        $data = $GUZZ->get($url, [
            'query' => $postdata,
        ])->getBody();
        $data = (string)$data;
        $data = json_decode($data,true);
        return $data;
    }
}