<?php
require 'lib.php';
$a = GetAccessToken('https://sandbox.api.payme.hsbc.com.hk', '77951c89-ebcc-43de-9a85-3a61f64e4d2d', 'lH38Q~ExDWe2IGuY_-M6p_tf8LF8QcttQRcNIdma');
$token =  $a['accessToken'];

$j = createPaymentRequest(
  'https://payme-proxy.sum-foods.com/',
  "https://sandbox.api.payme.hsbc.com.hk",
  $token,
  "ca7efc3f-d68a-4205-96bc-31e05ca5be2a",
  "b0dGSEtPWjZ2QlFaQmxIS0JHeWVVVzh3T0x2SVRFamhzQXdXenZzSWdIOD0=",
  [
    "totalAmount" => 3.77,
    "currencyCode" => "HKD",
    "effectiveDuration" => 600,
    "notificationUri" => "https=>//webhook.site/a8355331-d748-4951-bf6b-7e02f6fce605",
    "appSuccessCallback" => "www.example.com/success",
    "appFailCallback" => "www.example.com/failure",
    "merchantData" => [
      "orderId" => "ID12345678",
      "orderDescription" => "Description displayed to customer",
      "additionalData" => "Arbitrary additional data - logged but not displayed",
      "shoppingCart" => [
        [
          "category1" => "General categorization",
          "category2" => "More specific categorization",
          "category3" => "Highly specific categorization",
          "quantity" => 1,
          "price" => 1,
          "name" => "Item 1",
          "sku" => "SKU987654321",
          "currencyCode" => "HKD"
        ],
        [
          "category1" => "General categorization",
          "category2" => "More specific categorization",
          "category3" => "Highly specific categorization",
          "quantity" => 2,
          "price" => 1,
          "name" => "Item 2",
          "sku" => "SKU678951234",
          "currencyCode" => "HKD"
        ]
      ]
    ]
  ]
);
var_dump($j);
