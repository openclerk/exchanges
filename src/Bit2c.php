<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class Bit2c extends SimpleExchange {

  function getName() {
    return "Bit2c";
  }

  function getCode() {
    return "bit2c";
  }

  function getURL() {
    return "https://www.bit2c.co.il/";
  }

  function getBit2cCode($str) {
    switch (strtolower($str)) {
      case "ils": return "nis";
      default:
        return strtolower($str);
    }
  }

  /**
   * Bit2c does not have any API to list all available markets,
   * so we hardcode this.
   * Does not block.
   */
  public function fetchMarkets(Logger $logger) {
    return array(
      array('ils', 'btc'),
      array('ils', 'ltc'),
      array('btc', 'ltc'),
    );
  }

  function fetchAllRates(Logger $logger) {
    $result = array();
    foreach ($this->fetchMarkets($logger) as $pair) {
      $this->throttle($logger);

      $currency1 = $pair[0];
      $currency2 = $pair[1];

      $key = $this->getBit2cCode($currency2) . $this->getBit2cCode($currency1);
      $url = "https://www.bit2c.co.il/Exchanges/" . $key . "/Ticker.json";
      $logger->info($url);

      $json = Fetch::jsonDecode(Fetch::get($url));

      if (!isset($json['ll'])) {
        throw new ExchangeRateException("Could not find any last rate");
      }

      $rate = array(
        "currency1" => $currency1,
        "currency2" => $currency2,
        "bid" => $json['h'],
        "ask" => $json['l'],
        "last_trade" => $json['ll'],
        "volume" => $json['a'],
        "avg" => $json['ll'],
      );

      $result[] = $rate;
    }

    return $result;
  }

}
