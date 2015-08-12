<?php

require_once("coinbase/lib/Coinbase.php");

class Gateway {
  private $_config;
  private $_module;
  private $_basket;

  // Coinbase
  private $coinbase;
  private $code;
  private $order_number;

	public function __construct($module = false, $basket = false) {
      $this->_module = $module;
      $this->_basket =& $GLOBALS['cart']->basket;
      $this->_config =& $GLOBALS['config'];

      $default_currency = $GLOBALS['config']->get('config', 'default_currency');
      $total = strval($this->_basket['total']);
      $this->order_number = strval($this->_basket['cart_order_id']);

      $this->coinbase = Coinbase::withApiKey($this->_module['api_key'], $this->_module['api_secret']);

      $response = $this->coinbase->createButton("Order ".$this->order_number, $total, $default_currency, $this->order_number, array(
        "success_url" => $GLOBALS['storeURL'].'/index.php?_g=rm&type=gateway&cmd=process&module=Bitcoin',
        "cancel_url" => $GLOBALS['storeURL'].'/index.php?_g=rm&type=gateway&cmd=process&module=Bitcoin',
        "auto_redirect" => true
      ));

      $this->code = $response->button->code;
	}

  public function transfer() {
    $transfer	= array(
      'action'	=> 'https://www.coinbase.com/checkouts/'.$this->code,
      'method'	=> 'get',
      'target'	=> '_self',
      'submit'	=> 'auto',
    );
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
    $coinbase_order = $this->coinbase->getOrder($_GET["order"]["id"]);

    $order = Order::getInstance();

    if ($coinbase_order->status == "mispaid") {
      $order->orderStatus(Order::ORDER_PENDING, $this->order_number);
      $order->paymentStatus(Order::PAYMENT_PENDING, $this->order_number);

      $transData['notes'] = "Bitcoin payment mispaid";
      $order->logTransaction($transData);

      unset($_GET["order"]["refund_address"]);
      $GLOBALS['gui']->setError("Your Bitcoin payment was the incorrect amount. Please contact support to resolve your order.");
    }
    elseif ($coinbase_order->status == "expired") {
      $order->orderStatus(Order::ORDER_PENDING, $this->order_number);
      $order->paymentStatus(Order::PAYMENT_PENDING, $this->order_number);

      $transData['notes'] = "Bitcoin payment expired";
      $order->logTransaction($transData);

      unset($_GET["order"]["refund_address"]);
      $GLOBALS['gui']->setError("Your Bitcoin payment has expired before you could make your payment. Please contact support to resolve your order.");
    }
    else {
      $order->orderStatus(Order::ORDER_PROCESS, $this->order_number);
      $order->paymentStatus(Order::PAYMENT_SUCCESS, $this->order_number);

      $transData['notes'] = "Bitcoin payment successful";
      $order->logTransaction($transData);

      unset($_GET["order"]);
    }

    httpredir(currentPage(array('_g', 'type', 'cmd', 'module'), array('_a' => 'complete')));
  }

  public function form() {
    return false;
  }
}
