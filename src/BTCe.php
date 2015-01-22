<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class BTCe extends SimpleExchange {

  function getName() {
    return "BTC-e";
  }

  function getCode() {
    return "btce";
  }

  function getURL() {
    return "https://btc-e.com/";
  }

  /**
   * Convert the given BTC-e currency code
   * into the openclerk/currencies currency code (lowercase, three characters)
   */
  function getCurrencyCode($str) {
    switch (strtolower($str)) {
      // exceptions
      case "cnh": return "cny";
      case "doge": return "dog";

      // otherwise, uses expected currency codes
      default:
        return strtolower($str);
    }
  }

  function getBTCeCode($str) {
    switch (strtolower($str)) {
      // exceptions
      case "cny": return "cnh";
      case "dog": return "doge";

      // otherwise, uses expected currency codes
      default:
        return strtolower($str);
    }
  }

  public function fetchMarkets(Logger $logger) {
    $url = "https://btc-e.com/api/3/info";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    $result = array();
    foreach ($json['pairs'] as $pair => $info) {
      $pairs = explode("_", $pair, 2);
      $currency1 = $this->getCurrencyCode($pairs[0]);
      $currency2 = $this->getCurrencyCode($pairs[1]);

      $result[] = array($currency2, $currency1);
    }

    return $result;
  }

  function fetchAllRates(Logger $logger) {
    $result = array();
    $keys = array();
    foreach ($this->fetchMarkets($logger) as $pair) {
      $keys[] = $this->getBTCeCode($pair[1]) . "_" . $this->getBTCeCode($pair[0]);
    }

    $url = "https://btc-e.com/api/3/ticker/" . implode("-", $keys);
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    foreach ($this->fetchMarkets($logger) as $pair) {
      $key = $this->getBTCeCode($pair[1]) . "_" . $this->getBTCeCode($pair[0]);

      $currency1 = $pair[0];
      $currency2 = $pair[1];

      $rate = $json[$key];
      if (!isset($rate['last'])) {
        throw new ExchangeRateException("Could not find any last rate for $key");
      }

      $rate = array(
        "currency1" => $currency1,
        "currency2" => $currency2,
        "last_trade" => $rate['last'],
        "bid" => $rate['sell'],
        "ask" => $rate['buy'],
        "avg" => $rate['avg'],
        "volume" => $rate['vol_cur'],
        "high" => $rate['high'],
        "low" => $rate['low'],
      );

      $result[] = $rate;
    }

    return $result;
  }

}
