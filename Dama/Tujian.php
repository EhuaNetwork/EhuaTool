<?php


namespace Ehua\Dama;

/**
 * 图鉴滑块打码
 * Class Tujian
 * @package Ehua\Dama
 */
class Tujian
{
    static function init($path,$cfg)
    {

        $reportUrl = 'http://api.ttshitu.com/predict';
//待识别的图片
        $img_content = file_get_contents($path);
        $image = base64_encode($img_content);
        $ch = curl_init();
//一、图片文字类型(默认 3 数英混合)：
//1 : 纯数字
//1001：纯数字2
//2 : 纯英文
//1002：纯英文2
//3 : 数英混合
//1003：数英混合2
//4 : 闪动GIF
//7 : 无感学习(独家)
//11 : 计算题
//1005:  快速计算题
//16 : 汉字
//32 : 通用文字识别(证件、单据)
//66:  问答题
//49 :recaptcha图片识别 参考 https://shimo.im/docs/RPGcTpxdVgkkdQdY
//二、图片旋转角度类型：
//29 :  旋转类型

//三、图片坐标点选类型：
//19 :  1个坐标
//20 :  3个坐标
//21 :  3 ~ 5个坐标
//22 :  5 ~ 8个坐标
//27 :  1 ~ 4个坐标
//48 : 轨迹类型
//四、缺口识别
//18：缺口识别
//五、拼图识别
//53：拼图识别
//        $postFields = array('username' => '',    //改成你自己的
//            'password' => '',    //改成你自己的
//            'typeid' => '33',  //改成你需要的
//            'image' => $image
//        );
        $postFields = array('username' => $cfg['name'],    //改成你自己的
            'password' => $cfg['pwd'],    //改成你自己的
            'typeid' => '33',  //改成你需要的
            'image' => $image,
        );
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $reportUrl);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $data = curl_exec($ch);
        curl_close($ch);
//调试信息
//        var_dump("返回结果:" . $data);
        if (json_decode($data)->success) {
            $result = json_decode($data)->data->result;//识别的结果
            return $result;
        } else {
            $message = json_decode($data)->message;//识别的结果
            return 0;
        }

    }
}