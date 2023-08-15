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
  $data = $data ?? random_bytes(16);
  assert(strlen($data) == 16);
  $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function createPaymentRequest($jsURL, $baseUrl, $token, $keyId, $secret, $body)
{
  $client = new Client([
    'timeout'  => 5.0,
  ]);
  $response = $client->request('POST', $jsURL, [
    'json' => [
      'api_base_url' => $baseUrl,
      'access_token' => $token,
      'signing_key_id' => $keyId,
      'signing_key' => $secret,
      'body' => $body,
    ],
  ]);
  return json_decode($response->getBody()->__toString(), true);
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
