<?php
require 'lib.php';
$a = GetAccessToken('https://sandbox.api.payme.hsbc.com.hk', '77951c89-ebcc-43de-9a85-3a61f64e4d2d', 'lH38Q~ExDWe2IGuY_-M6p_tf8LF8QcttQRcNIdma');
$token =  $a['accessToken'];

createPaymentRequest(
  "https://sandbox.api.payme.hsbc.com.hk",
  $token,
  "ca7efc3f-d68a-4205-96bc-31e05ca5be2a",
  "b0dGSEtPWjZ2QlFaQmxIS0JHeWVVVzh3T0x2SVRFamhzQXdXenZzSWdIOD0=",
  "21092956-3359-46f6-bd5e-4378eadf8053"
);
// $str = 'dan.chen';
// echo  hash('sha256', 'Message');
// echo date("Y-m-d\TH:i:sP");
