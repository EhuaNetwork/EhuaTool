<?php

namespace Ehua\Pay\Paypal;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;

class Paypal
{
    public $client;
    public function __construct($config)
    {
        $clientId = $config['clientId'];
        $clientSecret = $config['clientSecret'];

        $environment = new SandboxEnvironment($clientId, $clientSecret);
        $this->client = new PayPalHttpClient($environment);
    }
    /**
     * @param $money            支付金额
     * @param $orderId          内部订单号
     * @param $currency_code    币种
     * @param $cancel_url       回调地址
     * @param $return_url       异步回调知道
     * @return false|\PayPalHttp\HttpResponse
     * @throws \PayPalHttp\IOException
     */
    public function pay($money = 0, $orderId = null, $currency_code = 'NZD', $cancel_url = '/', $return_url = '/')
    {
        $client = $this->client;

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => $orderId,
                "amount" => [
                    "value" => $money,
                    "currency_code" => $currency_code
                ]
            ]],
            "application_context" => [
                "cancel_url" => $cancel_url,
                "return_url" => $return_url,
            ]
        ];

        try {
            // Call API with your client and get a response for your call
            $response = $client->execute($request);

            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            return $response;
        } catch (HttpException $ex) {
            return false;
//            echo $ex->statusCode;
//            dd($ex->getMessage());
        }
    }

    /**
     * 回调验证
     * @return void
     * @throws \PayPalHttp\IOException
     */
    public function notify($orderid)
    {
        $client = $this->client;
        // Here, OrdersCaptureRequest() creates a POST request to /v2/checkout/orders
        // $response->result->id gives the orderId of the order created above
        $request = new OrdersCaptureRequest($orderid);
        $request->prefer('return=representation');
        try {
            // Call API with your client and get a response for your call
            $response = $client->execute($request);

            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            return $response;
        } catch (HttpException $ex) {
            return false;
            //  echo $ex->statusCode;
            //  dd($ex->getMessage());
        }
    }
}