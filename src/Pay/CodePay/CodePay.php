<?php

namespace Ehua\Pay\CodePay;

class CodePay
{
    public $epay_config;

    public function __construct($config)
    {

//支付接口地址
        $this->epay_config['apiurl'] = $config['apiurl'];
//商户ID
        $this->epay_config['pid'] = $config['pid'];
//商户密钥
        $this->epay_config['key'] = $config['key'];
        $this->epay_config['notify_url'] = $config['notify_url'];
        $this->epay_config['return_url'] =  $config['return_url'];

    }

    /**
     * @param $data     trade_no    type  name   money
     * @return string
     */
    public function create($data)
    {


        /**************************请求参数**************************/
        $notify_url =$this->epay_config['notify_url'];
//需http://格式的完整路径，不能加?id=123这类自定义参数

//页面跳转同步通知页面路径
        $return_url =$this->epay_config['return_url'];
//需http://格式的完整路径，不能加?id=123这类自定义参数

//商户订单号
        $out_trade_no = $data['trade_no'];
//商户网站订单系统中唯一订单号，必填

//支付方式（可传入alipay,wxpay,qqpay,bank,jdpay）
        $type = $data['type'];
//商品名称
        $name = $data['name'];
//付款金额
        $money = $data['money'];


        /************************************************************/
//构造要请求的参数数组，无需改动
        $parameter = array(
            "pid" => $this->epay_config['pid'],
            "type" => $type,
            "notify_url" => $notify_url,
            "return_url" => $return_url,
            "out_trade_no" => $out_trade_no,
            "name" => $name,
            "money" => $money,
        );

//建立请求
        $epay = new EpayCore($this->epay_config);
        $html_text = $epay->pagePay($parameter);
        return $html_text;
    }

    public function notify_url()
    {

//计算得出通知验证结果
        $epay = new EpayCore($this->epay_config);
        $verify_result = $epay->verifyNotify();

        if ($verify_result) {//验证成功

            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];

            //优码付交易号
            $trade_no = $_GET['trade_no'];

            //交易状态
            $trade_status = $_GET['trade_status'];

            //支付方式
            $type = $_GET['type'];

            //支付金额
            $money = $_GET['money'];

            if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
            }

            //验证成功返回
            return "success";
        } else {
            //验证失败
            return "fail";
        }
    }


    public function return_url(){
        //计算得出通知验证结果
        $epay = new EpayCore($this->epay_config);
        $verify_result = $epay->verifyReturn();

        if($verify_result) {//验证成功

            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];

            //支付宝交易号
            $trade_no = $_GET['trade_no'];

            //交易状态
            $trade_status = $_GET['trade_status'];

            //支付方式
            $type = $_GET['type'];


            if($_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
            }
            else {
                echo "trade_status=".$_GET['trade_status'];
            }

            echo "<h3>验证成功</h3><br />";
        }
        else {
            //验证失败
            echo "<h3>验证失败</h3>";
        }
    }
}