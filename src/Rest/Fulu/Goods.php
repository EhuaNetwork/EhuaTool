<?php


namespace Ehua\Rest\Fulu;


class Goods extends Common
{
    /**
     * fulu.goods.template.get 获取商品模板接口在线调试（沙箱环境）
     * 1、直充商品必须调用商品模板，卡密商品无需调用商品模板；
     *
     * 2、获取商品模板信息（请求参数template_id，通过商品接口获得）；
     *
     * POST请求，Content-Type必须设置为：application/json；
     *
     * 3、商品模板接口对接步骤： 3.1、通过商品编号，调用“获取商品信息接口”，获取商品模板编号；
     *
     * 3.2、通过商品模板编号，调用“获取商品模板接口”，获取商品模板信息；
     *
     * 3.3、商品模板是json格式字符串，需要渲染到前端，用户充值时，由用户自行选择游戏区服、充值方式等信息；
     * @param $id
     * @return bool|string
     */
    public function template($id)
    {
        $biz_content = [
            'template_id' => $id,//商品模板编号
        ];

        $data = [
            'app_key' => $this->app_key,
            'method' => 'fulu.goods.template.get',
            'timestamp' => date('Y-m-d H:i:s', time()),
            'version' => '2.0',
            'format' => 'json',
            'charset' => 'utf-8',
            'sign_type' => 'md5',
            'app_auth_token' => '',
            'biz_content' => json_encode($biz_content),
        ];
        $data['sign'] = $this->getSign($data);
        return $this->http($data);
    }

    /**
     * fulu.goods.list.get 获取商品列表接口在线调试（沙箱环境）
     * 获取商品列表接口；
     *
     * POST请求，Content-Type必须设置为：application/json；
     */
    public function list()
    {
        $biz_content = [
//                'first_category_id'=>'219',//商品分类Id（一级）（预留参数，暂无商品分类接口提供）
//                'second_category_id'=>'708',//商品分类Id（二级）（预留参数，暂无商品分类接口提供）
//                'third_category_id'=>'709',//商品分类Id（三级）（预留参数，暂无商品分类接口提供）
//                'product_id'=>'10000001',//商品编号
//                'product_name'=>'ww腾讯Q币直充一元',//商品名称
//                'product_type'=>'直充',//库存类型：卡密、直充
            'face_value' => '100',
        ];

        $data = [
            'app_key' => $this->app_key,
            'method' => 'fulu.goods.list.get',
            'timestamp' => date('Y-m-d H:i:s', time()),
            'version' => '2.0',
            'format' => 'json',
            'charset' => 'utf-8',
            'sign_type' => 'md5',
            'app_auth_token' => '',
            'biz_content' => json_encode($biz_content),
        ];
        $data['sign'] = $this->getSign($data);
        return $this->http($data);
    }


    /**
     * fulu.goods.info.get 获取商品信息接口在线调试（沙箱环境）
     * 获取商品信息接口；
     *
     * POST请求，Content-Type必须设置为：application/json；
     */
    public function info($id)
    {
        $biz_content = [
            'product_id' => $id,
        ];

        $data = [
            'app_key' => $this->app_key,
            'method' => 'fulu.goods.info.get',
            'timestamp' => date('Y-m-d H:i:s', time()),
            'version' => '2.0',
            'format' => 'json',
            'charset' => 'utf-8',
            'sign_type' => 'md5',
            'app_auth_token' => '',
            'biz_content' => json_encode($biz_content),
        ];
        $data['sign'] = $this->getSign($data);
        return $this->http($data);
    }


}