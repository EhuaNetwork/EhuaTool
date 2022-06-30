<?php


namespace Ehua\Pay\Stripe;



/**
 * 残缺版  需整理
 * composer  "paypal/paypal-checkout-sdk": "^1.0",
 * Class Stripe
 * @package Ehua\Pay\Stripe
 */
class Stripe
{
    public function init($orderid)
    {
        $str=<<<eol
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>安全支付</title>
</head>
<body>
<div style="text-align: center;width:100%;z-index:9999;position: fixed;top:0px;left:0px;height:600px;background:#fff" id="asdasd">
    正在唤起支付 请稍后...
</div>
<iframe src="init2?orderid=$orderid" frameborder="0" id="asdas" style="width:100%;height:600px"></iframe>
</body>
</html>
<script src="https://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>

<script>

    $(function () {
       setTimeout(function (){
           $('#asdasd').hide()
           $('#asdas').show()
       },15000)
    })


</script>
eol;
        echo $str;die;

    }

    public function init2($orderid)
    {
        $str=<<<eol
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Accept a payment</title>
    <meta name="description" content="A demo of a payment on Stripe"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="checkout.css"/>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="checkout_js?orderid=$orderid" defer></script>
</head>
<body>
<!-- Display a payment form -->
<div style="z-index: 1">
    <form id="payment-form">
        <div id="payment-element">
            <!--Stripe.js injects the Payment Element-->
        </div>
        <button id="submit">
            <div class="spinner hidden" id="spinner"></div>
            <span id="button-text">Pay now</span>
        </button>
        <div id="payment-message" class="hidden"></div>
    </form>
</div>
</body>

</html>
eol;
        echo $str;die;

    }

    // public $orderinfo;
    // public function _initialize(){
    //     $id=request()->inupt('orderid');
    //   $this->orderinfo= db('dx_tiny_wmall_order')->where('id',$id)->find();

    // }

    public function checkout_js($orderid){
        $pk=config('stripe.pk');
        $money= db('dx_tiny_wmall_order')->where('ordersn',$orderid)->value('final_fee');
        $str=<<<eol
              // This is your test publishable API key.
const stripe = Stripe("$pk");

// The items the customer wants to buy
const items = [{ orderid: "$orderid",money: "$money" }];

let elements;

initialize();
checkStatus();

document
  .querySelector("#payment-form")
  .addEventListener("submit", handleSubmit);

// Fetches a payment intent and captures the client secret
async function initialize() {
  const { clientSecret } = await fetch("https://scps-api.cnshangji.cn/api/stripe/create?orderid=$orderid", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ items }),
  }).then((r) => r.json());

  elements = stripe.elements({ clientSecret });

  const paymentElement = elements.create("payment");
  paymentElement.mount("#payment-element");
}

async function handleSubmit(e) {
  e.preventDefault();
  setLoading(true);

  const { error } = await stripe.confirmPayment({
    elements,
    confirmParams: {
      // Make sure to change this to your payment completion page
      return_url: "http://scps.cnshangji.cn/addons/we7_wmall/template/vue/index.html#/pages/member/mine?i=1",
    },
  });

  // This point will only be reached if there is an immediate error when
  // confirming the payment. Otherwise, your customer will be redirected to
  // your `return_url`. For some payment methods like iDEAL, your customer will
  // be redirected to an intermediate site first to authorize the payment, then
  // redirected to the `return_url`.
  if (error.type === "card_error" || error.type === "validation_error") {
    showMessage(error.message);
  } else {
    showMessage("An unexpected error occured.");
  }

  setLoading(false);
}

// Fetches the payment intent status after payment submission
async function checkStatus() {
  const clientSecret = new URLSearchParams(window.location.search).get(
    "payment_intent_client_secret"
  );

  if (!clientSecret) {
    return;
  }

  const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

  switch (paymentIntent.status) {
    case "succeeded":
      showMessage("Payment succeeded!");
      break;
    case "processing":
      showMessage("Your payment is processing.");
      break;
    case "requires_payment_method":
      showMessage("Your payment was not successful, please try again.");
      break;
    default:
      showMessage("Something went wrong.");
      break;
  }
}

// ------- UI helpers -------

function showMessage(messageText) {
  const messageContainer = document.querySelector("#payment-message");

  messageContainer.classList.remove("hidden");
  messageContainer.textContent = messageText;

  setTimeout(function () {
    messageContainer.classList.add("hidden");
    messageText.textContent = "";
  }, 4000);
}

// Show a spinner on payment submission
function setLoading(isLoading) {
  if (isLoading) {
    // Disable the button and show a spinner
    document.querySelector("#submit").disabled = true;
    document.querySelector("#spinner").classList.remove("hidden");
    document.querySelector("#button-text").classList.add("hidden");
  } else {
    document.querySelector("#submit").disabled = false;
    document.querySelector("#spinner").classList.add("hidden");
    document.querySelector("#button-text").classList.remove("hidden");
  }
}
eol;

        echo $str;die;

    }

    public function create($orderid){
        if(empty($orderid)){
            echo 'error';die;
        }
        // This is your test secret API key.
        \Stripe\Stripe::setApiKey( config('stripe.sk'));

        function calculateOrderAmount(array $items): int {
            // Replace this constant with a calculation of the order's amount
            // Calculate the order total on the server to prevent
            // people from directly manipulating the amount on the client
            return ($items[0]->money)*100;
        }

        header('Content-Type: application/json');

        try {
            // retrieve JSON from POST body
            $jsonStr = file_get_contents('php://input');
//   file_put_contents('1.json',$jsonStr);
            // var_dump($jsonStr);die;;
            $jsonObj = json_decode($jsonStr);

            // Create a PaymentIntent with amount and currency
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => calculateOrderAmount($jsonObj->items),
                'currency' => 'hkd',
                'description'=>$orderid,
                'payment_method_types' => ['card'],
//                'automatic_payment_methods' => [
//                    'enabled' => 'true',
//                ],
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];

            echo json_encode($output);
        } catch (Error $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }


    public function webhook(){
        // This is your Stripe CLI webhook secret for testing your endpoint locally.
        $_key =  config('stripe.webhook');
        \Stripe\Stripe::setApiKey($_key);
        $payload = @file_get_contents('php://input');
        $event = null;
        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        }

        // Handle the event

        switch ($event->type) {
            case 'charge.succeeded':
                $succeeded = $event->data->object;

//                db('dx_tiny_wmall_order')->where('ordersn',$succeeded->description)->update(['status'=>1,'is_pay'=>1]);

                return 'true';
                break;
            default:
                echo 'Received unknown event type ' . $event->type;
                break;
        }
    }

}