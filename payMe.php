<?php
/*
 * Plugin Name: WooCommerce PayMe
 * Plugin URI: https://rudrastyh.com/woocommerce/payment-gateway-plugin.html
 * Description: Take credit card payments on your store.
 * Author: Misha Rudrastyh
 * Author URI: http://rudrastyh.com
 * Version: 1.0.1
 */

require 'lib.php';
// require 'vendor/autoload.php';

add_action('plugins_loaded', 'init_payme_gateway_class');

function init_payme_gateway_class()
{
  class WC_Gateway_PayMe extends WC_Payment_Gateway
  {
    public $merchantId = '';
    public $clientId = '';
    public $clientSecret = '';
    public $signingKeyId = '';
    public $signingKey = '';
    public $env = '';
    function  __construct()
    {
      $this->id = "payme_gateway";
      $this->has_fields = true;
      $this->method_title = "PayMe";
      $this->method_description = "PayMe payment gateway";
      $this->init_form_fields();
      $this->init_settings();
      $this->enabled = $this->get_option('enabled');
      $this->title = 'PayMe';
      $this->merchantId = $this->get_option('merchantId');
      $this->clientId = $this->get_option('clientId');
      $this->clientSecret = $this->get_option('clientSecret');
      $this->signingKeyId = $this->get_option('signingKeyId');
      $this->signingKey = $this->get_option('signingKey');
      $this->env = $this->get_option('env');
      add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }

    function WebhookData()
    {
      $body = file_get_contents("php://input");
      $object = json_decode($body, true);
      return $object;
    }

    function webhook()
    {
      $data = $this->WebhookData();
      if (Verify($data, $this->get_option("paymeKey"))) {
        if ($data["requestType"] == "UNIONPAY" && $data["tradeState"] == "SUCCESS") {
          $order = new WC_Order($data["orderNo"]);
          $order->payment_complete();
          error_log("webhook success");
          echo "success";
        }
      } else {
        echo "error";
        error_log("webhook error");
        header("HTTP/1.0 404 Not Found");
      }
    }

    function init_form_fields()
    {
      $this->form_fields = array(
        'enabled' => array(
          'title' => __('Enable/Disable', 'woocommerce'),
          'type' => 'checkbox',
          'label' => __('Enable payme', 'woocommerce'),
          'default' => 'yes'
        ),
        'merchantId' => array(
          'title' => __('Key', 'woocommerce'),
          'type' => 'text',
          'description' => __('Merchant ID', 'woocommerce'),
          'default' => __('', 'woocommerce'),
          'desc_tip'      => false,
        ),
        'clientId' => array(
          'title' => __('Client Id', 'woocommerce'),
          'type' => 'text',
          'description' => __('Client Id', 'woocommerce'),
          'default' => ''
        ),
        'clientSecret' => array(
          'title' => __('Client Secret', 'woocommerce'),
          'type' => 'text',
          'default' => ''
        ),
        'signingKeyId' => array(
          'title' => __('Signing Key Id', 'woocommerce'),
          'type' => 'text',
          'default' => ''
        ),
        'signingKey' => array(
          'title' => __('Signing Key', 'woocommerce'),
          'type' => 'text',
          'default' => ''
        ),
        'env' => [
          'title' => __('Env', 'woocommerce'),
          'type' => 'select',
          'default' => 'Test',
          'options' => [
            'test' => 'test',
            'live' => 'live',
            'prod' => 'prod'
          ]
        ]
      );
    }

    function process_payment($order_id)
    {

      return array(
        'result' => 'success',
        'redirect' => '/',
      );
    }
  }
}

function add_your_gateway_class($methods)
{
  $methods[] = 'WC_Gateway_payme';
  return $methods;
}

function printArray($arr, $level)
{
  if (gettype($arr) != "array") {
    error_log("level", $level, $arr);
  } else {
    foreach ($arr as $key => $value) {
      error_log($key);
      printArray($value, $level + 1);
    }
  }
}

add_filter('woocommerce_payment_gateways', 'add_your_gateway_class');
