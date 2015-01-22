<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class CoinsE extends SimpleExchange {

  function getName() {
    return "Coins-E";
  }

  function getCode() {
    return "coins-e";
  }

  function getURL() {
    return "https://www.coins-e.com/";
  }

  /**
   * Convert the given Coins-e currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    switch (strtoupper($str)) {
      // exceptions
      case "DOGE": return "dog";

      // otherwise return lowercase
      default:
        return strtolower($str);
    }
  }

  function fetchAllRates(Logger $logger) {
    $url = "https://www.coins-e.com/api/v2/markets/data/";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    $result = array();

    $retired = 0;
    foreach ($json as $key => $market) {
      if ($market['status'] == 'retired') {
        $retired++;
        continue;
      }
      if ($market['marketstat']['ltp'] == 0) {
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
        "last_trade" => $market['marketstat']['ltp'],
        "bid" => $market['marketstat']['bid'],
        "ask" => $market['marketstat']['ask'],
        "volume" => $market['marketstat']['24h']['volume'],
        "high" => $market['marketstat']['24h']['h'],
        "low" => $market['marketstat']['24h']['l'],
        "avg" => $market['marketstat']['24h']['avg_rate'],
      );

      $result[] = $rate;
    }

    $logger->info("Ignored $retired retired markets");

    return $result;
  }

}
