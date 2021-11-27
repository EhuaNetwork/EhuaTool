<?php

namespace Ehua\Dama;
/**
 * 超人打码操作类 http://www.chaorendama.com/
 * Class Chaor
 * @package Ehua\Dama
 */
class Chaor
{

    /**
     * 查询剩余点数
     * @param $user     用户名
     * @param $pass     密码
     * @return bool|string
     */
    function get_info($user, $pass)
    {
        $http = curl_init();
        curl_setopt($http, CURLOPT_URL, 'http://api2.sz789.net:88/GetUserInfo.ashx');
        curl_setopt($http, CURLOPT_RETURNTRANSFER, 1);
        $postData = 'username=' . $user . '&password=' . $pass;
        curl_setopt($http, CURLOPT_POSTFIELDS, $postData);
        $data = curl_exec($http);
        curl_close($http);
        return $data;
    }

    /**
     * 识别图片文字
     * @param $user     用户名
     * @param $pass     密码
     * @param $softid   识别类型
     * @param $imgdata  图片数据
     * @return bool|string
     */
    function recv_byte($user, $pass, $softid, $imgdata)
    {
        $http = curl_init();
        curl_setopt($http, CURLOPT_URL, 'http://api2.sz789.net:88/RecvByte.ashx');
        curl_setopt($http, CURLOPT_RETURNTRANSFER, 1);
        $postData = 'username=' . $user . '&password=' . $pass . '&softId=' . $softid . '&imgdata=' . $imgdata;
        curl_setopt($http, CURLOPT_POSTFIELDS, $postData);
        $data = curl_exec($http);
        curl_close($http);
        return $data;
    }

    /**
     * 报告错误,只在返回识别结果且识别错误时，使用该函数
     * @param $user
     * @param $pass
     * @param $imgid
     * @return bool|string
     */
    function report_err($user, $pass, $imgid)
    {
        $http = curl_init();
        curl_setopt($http, CURLOPT_URL, 'http://api2.sz789.net:88/ReportError.ashx');
        curl_setopt($http, CURLOPT_RETURNTRANSFER, 1);
        $postData = '&username=' . $user . '&password=' . $pass . '&imgid=' . $imgid;
        curl_setopt($http, CURLOPT_POSTFIELDS, $postData);
        $data = curl_exec($http);
        curl_close($http);
        return $data;
    }




//    //超人云账号配置信息
//$user = 'Ehua'; //超人云账号
//$pass = '150638'; //超人云密码
//$softid = '76315';//缺省为0,作者必填自己的软件id,以保证分成收入.
//
//$bin = file_get_contents($file_dir);
//$imgdata = bin2hex($bin); //将图片二进制转为十六进制字符串上传到服务器
//
//$ORC=new Chaor;
//    //查询帐号信息
//echo '----帐号信息----<br />';
//$info = $ORC->get_info($user, $pass);
//$infoArray = json_decode($info, true);
//var_dump($infoArray);
//    //识别图片文字
//echo '<br />----识别图片文字----<br />';
//$result = $ORC->recv_byte($user, $pass, $softid, $imgdata);
//$reArray = json_decode($result, true);
//dd($reArray);
//if ($reArray["info"]==1)
//{
//echo "识别结果:".$reArray["result"].",图片ID:".$reArray["imgId"]."<br />";
//}
//
//else
//{
//    echo "识别失败<br />";
//}
//echo '<br />识别完成...<br />';
//
///*
////报告错误,只有识别成功并且识别错误时,调用此函数才有效
//if($reArray["info"]!=1)
//{
//    report_err($user,$pass,$reArray["imgId"]);
//    echo "已报告错误";
//}
//*/
//



}