<?php


namespace Ehua\Pay\WxPay;


class Notify
{
    public function init()
    {
        try {
            $mchid = config('mchid');          //微信支付商户号 PartnerID 通过微信支付商户资料审核后邮件发送
            $appid = config('appid');  //公众号APPID 通过微信支付商户资料审核后邮件发送
            $apiKey = config('apiKey');   //https://pay.weixin.qq.com 帐户设置-安全设置-API安全-API密钥-设置API密钥
            $wxPay = new NotifyService($mchid, $appid, $apiKey);
            $result = $wxPay->notify();
            if ($result) {
                //现金支付金额：$result['cash_fee']
                //订单金额：$result['total_fee']
                //商户订单号：$result['out_trade_no']
                //付款银行：$result['bank_type']
                //货币种类：$result['fee_type']
                //是否关注公众账号：$result['is_subscribe']
                //用户标识：$result['openid']
                //业务结果：$result['result_code']  SUCCESS/FAIL
                //支付完成时间：$result['time_end']  格式为yyyyMMddHHmmss
//                $LOG = [
//                    'name' => '微信服务器回调日志',
//                    'out_trade_no' => $result['out_trade_no'],
//                    'log' => json_encode($result, true),
//                    'create_time' => date('Y-m-d H:i:s', time())];
//                db('order_log')->insert($LOG);



                $data = db('order')
                    ->where('orderid', $result['out_trade_no'])
                    ->where('money', $result['total_fee']/100)
                    ->where('status', -1)
                    ->find();
                if (empty($data)) {
                    echo "<xml><return_code><![CDATA[FAIL]]></return_code></xml>";
                }
                $dat = [
                    'status' => 1,
                    'openid' => $result['openid'],
                    'out_trade_no' => $result['out_trade_no'],
                    'time_end' => $result['time_end'],
                    'bank_type' => $result['bank_type'],
                ];
                db('order')->where(['orderid' => $result['out_trade_no']])->update($dat);




                //todo 执行逻辑



                echo "<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>";
            } else {
                echo "<xml><return_code><![CDATA[FAIL]]></return_code></xml>";
            }

        } catch (Exception $exception) {
            $LOG = [
                'name' => '微信错误日志',
                'out_trade_no' => 1,
                'log' => $exception,
                'create_time' => date('Y-m-d H:i:s', time())];
            db('order_log')->insert($LOG);
        }
    }
}