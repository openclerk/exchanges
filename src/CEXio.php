<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class CEXio extends SimpleExchange {

  function getName() {
    return "CEX.io";
  }

  function getCode() {
    return "cexio";
  }

  function getURL() {
    return "https://cex.io/";
  }

  function getCEXioCode($str) {
    switch (strtolower($str)) {
      // exceptions
      case "dog": return "DOGE";

      // otherwise, uses expected currency codes
      default:
        return strtoupper($str);
    }
  }

  /**
   * CEX.io does not have any API to list all available markets,
   * so we hardcode this.
   * Does not block.
   */
  public function fetchMarkets(Logger $logger) {
    return array(
      array('usd', 'btc'),
      array('usd', 'ghs'),
      array('usd', 'ltc'),
      array('usd', 'dog'),
      array('usd', 'drk'),

      array('eur', 'btc'),
      array('eur', 'ltc'),
      array('eur', 'dog'),
      array('eur', 'drk'),

      array('btc', 'ghs'),
      array('btc', 'ltc'),
      array('btc', 'dog'),
      array('btc', 'drk'),
      array('btc', 'nmc'),
      array('btc', 'ixc'),
      array('btc', 'pot'),
      array('btc', 'anc'),
      array('btc', 'mec'),
      array('btc', 'wdc'),
      array('btc', 'ftc'),
      array('btc', 'dgb'),
      array('btc', 'usde'),
      array('btc', 'myr'),
      array('btc', 'aur'),

      array('ltc', 'ghs'),
      array('ltc', 'dog'),
      array('ltc', 'drk'),
      array('ltc', 'mec'),
      array('ltc', 'wdc'),
      array('ltc', 'anc'),
      array('ltc', 'ftc'),
    );
  }

  function fetchAllRates(Logger $logger) {
    $result = array();
    foreach ($this->fetchMarkets($logger) as $pair) {
      $this->throttle($logger);

      $currency1 = $pair[0];
      $currency2 = $pair[1];

      $url = "https://cex.io/api/ticker/" . $this->getCEXioCode($currency2) . "/" . $this->getCEXioCode($currency1);
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
        "volume" => $json['volume'],    // last 24h
        "low" => $json['low'],    // last 24h
        "high" => $json['high'],    // last 24h
      );

      $result[] = $rate;
    }

    return $result;
  }

}
