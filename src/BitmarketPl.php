<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class BitmarketPl extends SimpleExchange {

  function getName() {
    return "BitMarket.pl";
  }

  function getCode() {
    return "bitmarket_pl";
  }

  function getURL() {
    return "https://www.bitmarket.pl/";
  }

  function getBitmarketPlCode($cur) {
    return strtoupper($cur);
  }

  /**
   * BitMarket.pl does not have any API to list all available markets,
   * so we hardcode this.
   * Does not block.
   */
  public function fetchMarkets(Logger $logger) {
    return array(
      array('pln', 'btc'),
      array('pln', 'ltc'),
    );
  }

  function fetchAllRates(Logger $logger) {
    $result = array();
    foreach ($this->fetchMarkets($logger) as $pair) {
      $this->throttle($logger);

      $currency1 = $pair[0];
      $currency2 = $pair[1];

      $key = $this->getBitmarketPlCode($currency2) . $this->getBitmarketPlCode($currency1);
      $url = "https://www.bitmarket.pl/json/" . $key . "/ticker.json";
      $logger->info($url);

      $json = Fetch::jsonDecode(Fetch::get($url));

      if (!isset($json['last'])) {
        throw new ExchangeRateException("Could not find any last rate");
      }

      $rate = array(
        "currency1" => $currency1,
        "currency2" => $currency2,
        "bid" => $json['bid'],
        "ask" => $json['ask'],
        "last_trade" => $json['last'],
        "volume" => $json['volume'],
        "low" => $json['low'],
        "high" => $json['high'],
        "vwap" => $json['vwap'],
      );

      $result[] = $rate;
    }

    return $result;
  }

}
