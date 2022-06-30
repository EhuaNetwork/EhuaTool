<?php


namespace Ehua\Rest\Fulu;


class Order extends Common
{
    public function mobile()
    {
        $config=[
            'app_key'=>'eP7b5DEShVbohUYkgQA3wiFco6Y1bIHk/b7+dMeMJs0FXogJv6ijN0vbNcYFLvRX',
            'token'=>'25c543ef40e24907a10882ccbb784cca',
        ];

        (new \Ehua\Rest\Fulu\Order(false,$config))->mobile();
        $biz_content = [
            'charge_phone' => 15725517053,
            'charge_value' => 500,
            'customer_order_no' => uniqid(),//外部订单号
            'customer_price' => 500,//外部销售价
            'shop_type' => '京东',
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

        $this->http($data);
    }
}