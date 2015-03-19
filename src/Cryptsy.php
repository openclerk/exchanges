<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Openclerk\Config;
use \Monolog\Logger;
use \Apis\Fetch;
use \Apis\FetchException;

class Cryptsy extends SimpleExchange {

  function getName() {
    return "Cryptsy";
  }

  function getCode() {
    return "cryptsy";
  }

  function getURL() {
    return "https://www.cryptsy.com/";
  }

  /**
   * Convert the given Cryptsy currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    switch (strtoupper($str)) {
      // exceptions
      case "DOGE": return "dog";
      case "BC": return "bc1";

      // otherwise return lowercase
      default:
        return strtolower($str);
    }
  }

  function generatePostData($method, $req = array()) {
    $key = Config::get('exchange_cryptsy_key');   // aka Application Key
    $secret = Config::get('exchange_cryptsy_secret');   // aka Application/Device ID

    $req['method'] = $method;
    $mt = explode(' ', microtime());
    $req['nonce'] = $mt[1];

    // generate the POST data string
    $post_data = http_build_query($req, '', '&');

    // generate the extra headers
    $headers = array(
      'Sign: ' . hash_hmac('sha512', $post_data, $secret),
      'Key: ' . $key,
    );

    return array('post' => $post_data, 'headers' => $headers);
  }

  function fetchAllRates(Logger $logger) {

    $params = $this->generatePostData("getmarkets");

    $url = "https://www.cryptsy.com/api";
    $logger->info($url);

    $raw = Fetch::post($url, $params['post'], array(), $params['headers']);
    $json = Fetch::jsonDecode($raw);

    if (!$json['success']) {
      throw new ExchangeRateException($json['error']);
    }

    $result = array();

    foreach ($json['return'] as $market) {
      $key = $market['label'];

      if ($market['last_trade'] == 0) {
        $logger->info("Ignoring '$key' market: last trade price is 0");
        continue;
      }

      $currency1 = $this->getCurrencyCode($market['secondary_currency_code']);
      $currency2 = $this->getCurrencyCode($market['primary_currency_code']);

      $rate = array(
        "currency1" => $currency1,
        "currency2" => $currency2,
        "last_trade" => $market['last_trade'],
        "volume" => $market['current_volume'],    // 24 hour trading volume in this market
        // Cryptsy returns buy/sell incorrectly
        'low' => $market['low_trade'],
        'high' => $market['high_trade'],
        // bid/ask is part of the public API which is huge and causes scripts to crash
        // TODO: add #fetchMarkets() to selectively download bid/ask data
      );

      if ($this->shouldSwitch($currency1, $currency2)) {
        $rate = array(
          'currency1' => $rate['currency2'],
          'currency2' => $rate['currency1'],
          'last_trade' => 1 / $rate['last_trade'],
          'volume' => $rate['volume'] / $rate['last_trade'],
          'low' => $rate['low'] == 0 ? 0 : 1 / $rate['low'],
          'high' => $rate['high'] == 0 ? 0 : 1 / $rate['high'],
        );
      }

      $result[] = $rate;
    }

    return $result;
  }

  function shouldSwitch($cur1, $cur2) {
    return !\Exchange\CurrencyOrder::isOrdered($cur1, $cur2);
  }

}
