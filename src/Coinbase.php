<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class Coinbase extends SimpleExchange {

  function getName() {
    return "Coinbase";
  }

  function getCode() {
    return "coinbase";
  }

  function getURL() {
    return "https://www.coinbase.com";
  }

  /**
   * Convert the given Coinbase currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    return strtolower($str);
  }

  function fetchAllRates(Logger $logger) {
    $url = "https://coinbase.com/api/v1/currencies/exchange_rates";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    $result = array();

    foreach ($json as $key => $last_trade) {
      $pairs = explode("_to_", $key, 2);

      $currency1 = $this->getCurrencyCode($pairs[0]);
      $currency2 = $this->getCurrencyCode($pairs[1]);

      if ($currency1 != "btc") {
        // we're only interested in fiat -> BTC rates
        continue;
      }

      $rate = array(
        "currency1" => $currency2,
        "currency2" => $currency1,
        // only last trade is available
        "last_trade" => $last_trade,
      );

      $result[] = $rate;
    }

    return $result;
  }

}
