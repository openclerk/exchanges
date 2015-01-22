<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class Vircurex extends SimpleExchange {

  function getName() {
    return "Vircurex";
  }

  function getCode() {
    return "vircurex";
  }

  function getURL() {
    return "https://vircurex.com/";
  }

  /**
   * Convert the given Vircurex currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    switch (strtoupper($str)) {
      case "DOGE": return "dog";
      case "BC": return "bc1";

      default:
        return strtolower($str);
    }
  }

  function fetchAllRates(Logger $logger) {
    $url = "https://api.vircurex.com/api/get_info_for_currency.json";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    $result = array();

    $ignored = 0;
    foreach ($json as $pair2 => $pairs) {
      if ($pair2 == "status") {
        continue;
      }

      foreach ($pairs as $pair1 => $market) {
        if ($market['last_trade'] == 0 || $market['lowest_ask'] == 0 || $market['highest_bid'] == 0) {
          // ignore empty markets
          $ignored++;
          continue;
        }

        $currency1 = $this->getCurrencyCode($pair1);
        $currency2 = $this->getCurrencyCode($pair2);

        if (CurrencyOrder::hasOrder($currency1) && CurrencyOrder::hasOrder($currency2)) {
          if (!CurrencyOrder::isOrdered($currency1, $currency2)) {
            // do not duplicate ordered currencies
            continue;
          }
        }

        $rate = array(
          "currency1" => $currency1,
          "currency2" => $currency2,
          "last_trade" => $market['last_trade'],
          "volume" => $market['volume'],
          'bid' => $market['highest_bid'],
          'ask' => $market['lowest_ask'],
        );

        $result[] = $rate;
      }
    }

    $logger->info("Ignored " . $ignored . " markets with last trade price of 0");

    return $result;
  }

}
