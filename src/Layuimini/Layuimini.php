<?php


namespace Ehua\Layuimini;


class Layuimini
{

    /**
     * 构造request 中的json参数  返回数组格式 直接进行使用
     * @param $key      json key
     * @return array|mixed|null
     */
    static function request_json($key)
    {
        $temp = request()->param($key);
        $temp = json_decode($temp, true);
        return array_filter($temp);
    } 
}