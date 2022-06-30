<?php


namespace Ehua\Pay\Easypay;

/**
 * 文档地址：https://wechat-pay-easypayx.doc.coding.io/#5adcc107a5f36c2b20def95ab72ffe86
 * Class EasyPay
 * @package Ehua\Pay\Easypay
 */
class EasyPay
{
    public function __construct()
    {
        $this->host = "https://wechatpay-api.easypayx.com";
        $this->client_id = "a0c20ae938a450918498b536ce12c2a8";
        $this->client_secret = "c9ba27ca-c952-48ef-8805-a28ff814c95f";

    }

    /**
     * 授权接口获取登录token
     * @return mixed
     */
    public function auth()
    {
//        $data = "client_id= $this->client_id&client_secret=$this->client_secret&grant_type=client_credentials";
        $data = [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'client_credentials',
        ];
        $res = $this->http('/auth', $data);
        return json_decode($res, true);
    }


    /**
     * 授权接口获取登录token
     * @param $client_id        商户支付订单 ID（商户唯一）
     * @param $appid            appid
     * @param $description      商品描述
     * @param $order_amount     标价金额，单位为分
     * @param $order_currency   可选值：CNY , HKD , USD , EUR , GBP , JPY , CAD|string 标价币种，例如：CNY
     * @param $settle_currency  可选值：HKD , USD , EUR , GBP , JPY , CAD|string 结算币种，例如：HKD
     */
    public function pay_app($client_id, $appid, $description, $order_amount, $order_currency = 'HKD', $settle_currency = 'HKD')
    {
        $token = $this->auth()['access_token'];

        $data = [
            'client_id' => $client_id,
            'appid' => $appid,
            'description' => $description,
            'order_amount' => $order_amount,
            'order_currency' => $order_currency,
            'settle_currency' => $settle_currency,
        ];
        $res = $this->http('/v3/wechat_pay/payments/app', $data,["Authorization:Bearer $token"]);

        return json_decode($res, true);
    }


    public function http($url, $data, $header = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->host . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers = array_merge($headers, $header);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            die;
        }
        curl_close($ch);
        return $result;
    }
}

$a = new EasyPay();
$client_id = time();
$appid = 0;
$description = '测试';
$order_amount = 1;
$a->pay_app($client_id, $appid, $description, $order_amount);