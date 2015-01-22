<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class Anxpro extends SimpleExchange {

  function getName() {
    return "ANXPRO";
  }

  function getCode() {
    return "anxpro";
  }

  function getURL() {
    return "https://anxpro.com/";
  }

  /**
   * Convert the given ANXPRO currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   */
  function getCurrencyCode($str) {
    switch (strtoupper($str)) {
      // exceptions
      case "DOGE": return "dog";

      // otherwise, ANXPRO uses expected currency codes
      default:
        return strtolower($str);
    }
  }

  function getANXPROCode($str) {
    switch (strtolower($str)) {
      case "dog": return "DOGE";
      default:
        return strtoupper($str);
    }
  }

  /**
   * ANXPRO does not have any API to list all available markets,
   * so we hardcode this.
   * Does not block.
   */
  public function fetchMarkets(Logger $logger) {
    $markets = "BTCUSD,BTCHKD,BTCEUR,BTCCAD,BTCAUD,BTCSGD,BTCJPY,BTCCHF,BTCGBP,BTCNZD,LTCBTC,DOGEBTC,STRBTC,XRPBTC";
    $result = array();
    foreach (explode(",", $markets) as $market) {
      $market = str_replace("DOGE", "DOG", $market);
      $currency1 = $this->getCurrencyCode(substr($market, 0, 3));
      $currency2 = $this->getCurrencyCode(substr($market, 3, 3));
      $result[] = array($currency2, $currency1);
    }
    return $result;
  }

  function fetchAllRates(Logger $logger) {
    $result = array();
    foreach ($this->fetchMarkets($logger) as $pair) {
      $this->throttle($logger);

      $currency1 = $pair[0];
      $currency2 = $pair[1];

      $key = $this->getANXPROCode($currency2) . $this->getANXPROCode($currency1);
      $url = "https://anxpro.com/api/2/" . $key . "/money/ticker";
      $logger->info($url);

      $json = Fetch::jsonDecode(Fetch::get($url));

      if (!$json['data']['buy']['value']) {
        $logger->info("Ignoring pair $key: bid is zero");
        continue;
      }
      if (!$json['data']['sell']['value']) {
        $logger->info("Ignoring pair $key: ask is zero");
        continue;
      }

      if (!isset($json['data']['last']['value'])) {
        throw new ExchangeRateException("Could not find any last rate");
      }

      $rate = array(
        "currency1" => $currency1,
        "currency2" => $currency2,
        // it seems ANX gets these swapped around
        "bid" => $json['data']['buy']['value'],
        "ask" => $json['data']['sell']['value'],
        "last_trade" => $json['data']['last']['value'],
        "volume" => $json['data']['vol']['value'],
        "high" => $json['data']['high']['value'],
        "low" => $json['data']['low']['value'],
        "vwap" => $json['data']['vwap']['value'],
        "avg" => $json['data']['avg']['value'],
      );

      $result[$rate['currency1'] . $rate['currency2']] = $rate;
    }

    return $result;
  }

}
