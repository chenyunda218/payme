<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

function url($env)
{
  switch ($env) {
    case 'test':
      return 'https://sandbox.api.payme.hsbc.com.hk';
      break;
  }
}

function Base64JS($from)
{
  $dir = __DIR__;
  $output = null;
  $retval = null;
  exec("node $dir/node/index.js $from", $output, $retval);
  return $output[0];
}

function guidv4($data = null)
{
  // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
  $data = $data ?? random_bytes(16);
  assert(strlen($data) == 16);

  // Set version to 0100
  $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
  // Set bits 6-7 to 10
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

  // Output the 36 character UUID.
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function createPaymentRequest($baseUrl, $token, $keyId, $secret, $merchantId)
{
  $client = new Client([
    'timeout'  => 5.0,
  ]);
  $body = array(
    'totalAmount' => 200,
    'currencyCode' => 'HKD',
    'notificationUri' => "https://example.com/return",
  );
  $bodyString = json_encode($body);
  $sha256digest = hash('sha256', 'Message');
  // echo "$sha256digest<br>";
  $base64sha2562 = base64_encode($sha256digest);
  // echo $base64sha2562;
  $uuid = guidv4();
  // echo $date->format('Y-m-d\\TH:i:sP');
  $requestDateTime = date("Y-m-d\\TH:i:s") . '.000Z';
  // keyId="ca7efc3f-d68a-4205-96bc-31e05ca5be2a",algorithm="hmac-sha256",headers="(request-target) Api-Version Request-Date-Time Trace-Id Authorization Digest",signature="5V6H7ZfWaU4xCxZnOsWNVBX3HVC3jsPZB9hCRSGIPiQ="
  // keyId="ca7efc3f-d68a-4205-96bc-31e05ca5be2a",algorithm="hmac-sha256",headers="(request-target) Api-Version Request-Date-Time Trace-Id Authorization Digest",signature="ib1VYBebjemgYIbsh+DOnnRM/5wz3EoEqn++9HVfT4w="
  $headers = array(
    'Request-Date-Time' => $requestDateTime,
    'Api-Version' => '0.12',
    'Trace-Id' => $uuid,
    'Authorization' => "Bearer $token",
    'Digest' => '',
  );
  // var_dump($headers);

  // $response = $client->request('POST', "$baseUrl/payments/paymentrequests", [
  //   'headers' => $headers,
  // ]);
  // return json_decode($response->getBody()->__toString(), true);
}

function GetAccessToken($baseUrl, $clientId, $clientSecret)
{
  $client = new Client([
    'timeout'  => 5.0,
  ]);
  $response = $client->request('POST', "$baseUrl/oauth2/token", [
    'headers' => [
      'Api-Version' => '0.12',
      'Accept' => 'application/json',
    ],
    'form_params' => [
      'client_id' => $clientId,
      'client_secret' => $clientSecret,
    ]
  ]);
  return json_decode($response->getBody()->__toString(), true);
}
