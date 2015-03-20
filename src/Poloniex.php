<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class Poloniex extends SimpleExchange {

  function getName() {
    return "Poloniex";
  }

  function getCode() {
    return "poloniex";
  }

  function getURL() {
    return "https://poloniex.com/";
  }

  /**
   * Convert the given Poloniex currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    switch (strtoupper($str)) {
      // exceptions
      case "DOGE": return "dog";
      case "BLK": return "bc1";
      case "XUSD": return "usd";
      case "SJCX": return "sj1";

      // otherwise return lowercase
      default:
        return strtolower($str);
    }
  }

  /**
   * @param $currency1 3-character code
   * @param $currency2 3-character code
   */
  function needsSwitch($currency1, $currency2) {
    switch ($currency1 . $currency2) {
      case "btcusd":
        return true;

      default:
        return false;
    }
  }

  function fetchAllRates(Logger $logger) {
    $url = "https://poloniex.com/public?command=returnTicker";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    $result = array();

    foreach ($json as $key => $market) {
      if ($market['last'] == 0) {
        $logger->info("Ignoring '$key' market: last trade price is 0");
        continue;
      }

      $pair = explode("_", $key, 2);

      $currency1 = $this->getCurrencyCode($pair[0]);
      $currency2 = $this->getCurrencyCode($pair[1]);

      $rate = array(
        "currency1" => $currency1,
        "currency2" => $currency2,
        "last_trade" => $market['last'],
        "volume" => $market['quoteVolume'],
        'bid' => $market['highestBid'],
        'ask' => $market['lowestAsk'],
        'high' => $market['high24hr'],
        'low' => $market['low24hr'],
      );

      if ($this->needsSwitch($currency1, $currency2)) {
        $rate = array(
          "currency1" => $rate['currency2'],
          "currency2" => $rate['currency1'],
          "last_trade" => 1 / $rate['last_trade'],
          "volume" => $rate['volume'] / $rate['last_trade'],
          'bid' => $rate['ask'] == 0 ? 0 : 1 / $rate['ask'],
          'ask' => $rate['bid'] == 0 ? 0 : 1 / $rate['bid'],
          'high' => $rate['low'] == 0 ? 0 : 1 / $rate['low'],
          'low' => $rate['high'] == 0 ? 0 : 1 / $rate['high'],
        );
      }

      $result[] = $rate;
    }

    return $result;
  }

}
