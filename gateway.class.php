<?php

require_once "lib/CoinbaseAPI.php";

class Gateway {
  private $_config;
  private $_module;
  private $_basket;

  // Coinbase
  private $coinbase;
  private $order_number;
  private $default_currency;
  private $total;
  private $cb_url;

  public function __construct($module = false, $basket = false) {
    $this->_module = $module;
    $this->_basket =& $GLOBALS['cart']->basket;
    $this->_config =& $GLOBALS['config'];

    $this->default_currency = $GLOBALS['config']->get('config', 'default_currency');
    $this->total = strval($this->_basket['total']);
    $this->order_number = strval($this->_basket['cart_order_id']);

    $this->cb_url = 'coinbase';

    if ($this->_module['sandbox']) {
      $this->cb_url = 'sandbox.' . $this->cb_url;
    }

    $this->coinbase = new CoinbaseAPI($this->_module['api_key'], $this->_module['api_secret'], $this->cb_url);
  }

  public function transfer() {

    $return_url = $GLOBALS['storeURL'] . '/index.php?_g=rm&type=gateway&cmd=process&module=Bitcoin';
    $fields = [
      "amount" => $this->total,
      "currency" => $this->default_currency,
      "name" => $this->order_number,
      "success_url" => $return_url,
      "cancel_url" => $return_url,
      "auto_redirect" => true
    ];

    $response_checkout = $this->coinbase->call("checkouts", "POST", $fields);
    $checkout_id = $response_checkout->data->id;

    $transfer = [
      'action' => 'https://' . $this->cb_url . '.com/checkouts/' . $checkout_id,
      'method' => 'get',
      'target' => '_self',
      'submit' => 'auto',
    ];

    return $transfer;
  }

  public function repeatVariables() {
    return false;
  }

  public function fixedVariables() {
    return false;
  }

  public function call() {
    return false;
  }

  public function process() {
//    $coinbase_orders = $this->coinbase->call("checkouts/" . $this->code . "/orders");

//    $coinbase_order = $coinbase_orders->data[0];

    $coinbase_order = $this->coinbase->call("orders/" . $_GET["order"]["uuid"])->data;

    $order = Order::getInstance();

    if ($coinbase_order->status == "mispaid") {
      $order->orderStatus(Order::ORDER_PENDING, $this->order_number);
      $order->paymentStatus(Order::PAYMENT_PENDING, $this->order_number);

      $transData['notes'] = "Bitcoin payment mispaid";
      $order->logTransaction($transData);

      $GLOBALS['gui']->setError("Your Bitcoin payment was the incorrect amount. Please contact support to resolve your order.");
    }
    elseif ($coinbase_order->status == "expired") {
      $order->orderStatus(Order::ORDER_PENDING, $this->order_number);
      $order->paymentStatus(Order::PAYMENT_PENDING, $this->order_number);

      $transData['notes'] = "Bitcoin payment expired";
      $order->logTransaction($transData);

      $GLOBALS['gui']->setError("Your Bitcoin payment has expired before you could make your payment. Please contact support to resolve your order.");
    }
    else {
      $order->orderStatus(Order::ORDER_PROCESS, $this->order_number);
      $order->paymentStatus(Order::PAYMENT_SUCCESS, $this->order_number);

      $transData['notes'] = "Bitcoin payment successful";
      $order->logTransaction($transData);
    }

    httpredir(currentPage(array('_g', 'type', 'cmd', 'module'), array('_a' => 'complete')));
  }

  public function form() {
    return false;
  }
}
