<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class VirtEx extends SimpleExchange {

  function getName() {
    return "VirtEx";
  }

  function getCode() {
    return "virtex";
  }

  function getURL() {
    return "https://virtex.com/";
  }

  /**
   * VirtEx does not have any API to list all available markets,
   * so we hardcode this.
   * Does not block.
   */
  public function fetchMarkets(Logger $logger) {
    return array(
      array('usd', 'btc'),
      array('usd', 'ltc'),
      array('btc', 'ltc'),
      array('eur', 'btc'),
      array('eur', 'ltc'),
    );
  }

  function fetchAllRates(Logger $logger) {
    $result = array();
    foreach ($this->fetchMarkets($logger) as $pair) {
      $this->throttle($logger);

      $currency1 = $pair[0];
      $currency2 = $pair[1];

      $key = $currency2 . "-" . $currency1;
      $url = "https://api.virtex.com:443/v1/market/ticker?market=" . $key;
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
      );

      $result[] = $rate;
    }

    return $result;
  }

}
