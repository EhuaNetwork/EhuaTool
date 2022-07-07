<?php


namespace Ehua\Rest\Fulu;


class Order extends Common
{
    /**
     * fulu.order.mobile.add 话费下单接口在线调试（沙箱环境）
     * 接口介绍：话费商品下单接口，供合作方针对话费类型商品进行下单；
     *
     * 1、POST请求，Content-Type必须设置为：application/json；
     *
     * 2、接口是异步，接口调用成功(即下单成功)，不代表充值成功，最终“充值结果”，需要调用“订单查询接口”进行查询，由于是异步操作，建议间隔1-3s循环调用，直至最终结果；
     *
     * 3、“订单查询接口”必须接入；
     */
    public function mobile($phone, $money)
    {

        $biz_content = [
            'charge_phone' => $phone,//充值手机号
            'charge_value' => $money,//充值数额
            'customer_order_no' => uniqid(),//外部订单号
            'customer_price' => $money,//外部销售价
            'shop_type' => '系统',//店铺类型（PDD、淘宝、天猫、京东、苏宁、其他）；非必填字段，可忽略
            'external_biz_id' => 'C564982164',//透传字段
        ];

        $data = [
            'app_key' => $this->app_key,
            'method' => 'fulu.order.mobile.add',
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
     * fulu.order.direct.add 直充下单接口在线调试（沙箱环境）
     * 接口介绍：直充商品下单接口，供合作方针对直充类型商品进行下单；
     *
     * 1、POST请求，Content-Type必须设置为：application/json；
     * 2、接口是异步，接口调用成功(即下单成功)，不代表充值成功，最终“充值结果”，需要调用“订单查询接口”进行查询，由于是异步操作，建议间隔1-3s循环调用，直至最终结果；
     * 3、“订单查询接口”必须接入；
     * 4、直充商品必须调用商品模板，卡密商品无需调用商品模板；
     */
    public function direct($data, $template)
    {
        //模板：ChargeAccount-----下单参数：charge_account
        //模板：ChargeNum---------下单参数：buy_num
        //模板：ChargeGame--------下单参数：charge_game_name
        //模板：ChargeRegion------下单参数：charge_game_region
        //模板：ChargeServer------下单参数：charge_game_srv
        //模板：ChargeType--------下单参数：charge_type
        //模板：ChargeIp----------下单参数：charge_ip
        //模板：ChargePWD---------下单参数：charge_password
        //模板：ContactQQ---------下单参数：contact_qq
        //模板：ContactType-------下单参数：contact_tel
        $biz_content = [
            'product_id' => $data['product_id'],//商品编号
            'customer_order_no' => $data['order_no'],//外部订单号

            'charge_account' => $template['ChargeAccount'],//充值账号
            'buy_num' => $template['ChargeNum'],//购买数量


//            'remaining_number' => $data['product_id'],//剩余数量
//            'charge_game_role' => $data['product_id'],//充值游戏角色
//            'customer_price' => $data['product_id'],//外部销售价
//            'shop_type' => $data['product_id'],//店铺类型（PDD、淘宝、天猫、京东、苏宁、其他；非必填字段，可忽略
            'external_biz_id' => null,//透传字段
        ];


        if (isset($template['ChargeGame'])) {//充值游戏名称
            $biz_content['charge_game_name'] = $template['ChargeGame'];
        }
        if (isset($template['ChargeRegion'])) {//充值游戏区
            $biz_content['charge_game_region'] = $template['ChargeRegion'];
        }
        if (isset($template['ChargeServer'])) {//充值游戏服
            $biz_content['charge_game_srv'] = $template['ChargeServer'];
        }
        if (isset($template['ChargeType'])) {//充值类型
            $biz_content['charge_type'] = $template['ChargeType'];
        }
        if (isset($template['ChargePWD'])) {//充值密码，部分游戏类要传
            $biz_content['charge_password'] = $template['ChargePWD'];
        }
        if (isset($template['ChargeIp'])) {//下单真实Ip，区域商品要传
            $biz_content['charge_ip'] = $template['ChargeIp'];
        }
        if (isset($template['ContactQQ'])) {//联系QQ
            $biz_content['contact_qq'] = $template['ContactQQ'];
        }
        if (isset($template['ContactType'])) {//联系电话
            $biz_content['contact_tel'] = $template['ContactType'];
        }

        $data = [
            'app_key' => $this->app_key,
            'method' => 'fulu.order.direct.add',
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
     * fulu.order.card.add 卡密下单接口在线调试（沙箱环境）
     * 接口介绍：卡密商品取卡接口，供合作方针对卡密类型商品进行下单；
     *
     * 1、POST请求，Content-Type必须设置为：application/json；
     *
     * 2、接口是异步，接口调用成功(即下单成功)，不代表充值成功，最终“充值结果”，需要调用“订单查询接口”进行查询，由于取卡是异步操作，建议间隔1-3s循环调用，直至最终结果；
     *
     * 3、此接口不会返回卡密数据，需要再调用“订单查询接口”获取卡密信息；
     *
     * 4、“订单查询接口”必须接入；
     */
    public function card($data,$template)
    {
        $biz_content = [
            'product_id' => $data['product_id'],//商品编号
            'buy_num' => $template['ChargeNum'],//购买数量
            'customer_order_no' => $data['order_no'],//外部订单号
//            'customer_price' => $data['price'],//外部销售价
//            'shop_type' =>  null,//店铺类型（PDD、淘宝、天猫、京东、苏宁、其他）；非必填字段，可忽略
//            'external_biz_id' => null,//透传字段
        ];
        $data = [
            'app_key' => $this->app_key,
            'method' => 'fulu.order.card.add',
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
     * fulu.order.info.get 订单查询接口在线调试（沙箱环境）
     * 接口介绍：查询订单详情接口，供合作方查询订单充值结果时调用；
     *
     * 1、POST请求，Content-Type必须设置为：application/json；
     * 2、如果是卡密取卡订单，此接口会返回卡密数据；
     * 3、卡密为密文，需要进行解密使用，详情请查看右侧常见问题->4.卡密解密说明；
     */
    public function info($order_no)
    {
        $biz_content = [
            'customer_order_no' => $order_no,//外部订单号
        ];
        $data = [
            'app_key' => $this->app_key,
            'method' => 'fulu.order.info.get',
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
     * 基础数据（测试使用）：
     *
     * AppKey：i4esv1l+76l/7NQCL3QudG90Fq+YgVfFGJAWgT+7qO1Bm9o/adG/1iwO2qXsAXNB
     * AppSecret：0a091b3aa4324435aab703142518a8f7
     * TestCardnumber：12nCp6X/nALmrvr1erxK+D4L8n/kqz/RItKWUfvZrCU=
     * TestCardpasswrod：9HeOgdv+NpLihh2+5Gm0Mj4L8n/kqz/RItKWUfvZrCU=
     * 注意：第三方依赖：commons-codec-1.11.jar、gson-2.8.5.jar、bcprov-jdk15on.jar；卡密解密中对于强加密长度超过128的，需要替换jre/lib/security下两个jar包，“ocal_policy.jar ”和“US_export_policy.jar”下载及参考文档：https://blog.csdn.net/tomatocc/article/details/85096911
     * 解密方法（示例仅供参考）
     * @param $enpass
     * @return string
     */
    public function decode($enpass)
    {
        $encryptString = base64_decode($enpass);
        $decryptedpass = rtrim(openssl_decrypt($encryptString, 'aes-256-ecb', $this->secret, OPENSSL_RAW_DATA));

        return trim($decryptedpass);
    }



}