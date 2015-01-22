<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class Bter extends SimpleExchange {

  function getName() {
    return "BTER";
  }

  function getCode() {
    return "bter";
  }

  function getURL() {
    return "https://bter.com/";
  }

  /**
   * Convert the given BTER currency code (uppercase)
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
    $url = "http://data.bter.com/api/1/tickers";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    $result = array();

    foreach ($json as $key => $market) {
      $pairs = explode("_", $key, 2);

      $currency1 = $this->getCurrencyCode($pairs[0]);
      $currency2 = $this->getCurrencyCode($pairs[1]);

      $rate = array(
        "currency1" => $currency2,
        "currency2" => $currency1,
        "bid" => $market['buy'],
        "ask" => $market['sell'],
        "last_trade" => $market['last'],
        "volume" => $market['vol_' . $pairs[0]],
        "high" => $market['high'],
        "low" => $market['low'],
      );

      $result[] = $rate;
    }

    return $result;
  }

}
