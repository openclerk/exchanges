<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class CryptoTrade extends SimpleExchange {

  function getName() {
    return "Crypto-Trade";
  }

  function getCode() {
    return "crypto-trade";
  }

  function getURL() {
    return "https://crypto-trade.com/";
  }

  /**
   * Convert the given Crypto-Trade currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    switch (strtolower($str)) {
      // exceptions
      case "doge": return "dog";

      // otherwise return lowercase
      default:
        return strtolower($str);
    }
  }

  function fetchAllRates(Logger $logger) {
    $url = "https://crypto-trade.com/api/1/tickers";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    if ($json['status'] != "success") {
      throw new ExchangeRateException($json['message']);
    }

    $result = array();

    foreach ($json['data'][0] as $key => $market) {
      if ($market['last'] == 0) {
        $logger->info("Ignoring '$key' market: last trade price is 0");
        continue;
      }

      $pairs = explode("_", $key, 2);

      $currency1 = $this->getCurrencyCode($pairs[0]);
      $currency2 = $this->getCurrencyCode($pairs[1]);

      $rate = array(
        "currency1" => $currency2,
        "currency2" => $currency1,
        // only last trade is available
        "last_trade" => $market['last'],
        "bid" => $market['max_bid'],
        "ask" => $market['min_ask'],
        "volume" => $market['vol_' . $pairs[0]],
        "high" => $market['high'],
        "low" => $market['low'],
      );

      $result[] = $rate;
    }

    return $result;
  }

}
