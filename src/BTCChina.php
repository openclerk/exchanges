<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class BTCChina extends SimpleExchange {

  function getName() {
    return "BTCChina";
  }

  function getCode() {
    return "btcchina";
  }

  function getURL() {
    return "https://www.btcchina.com/";
  }

  function fetchAllRates(Logger $logger) {
    $url = "https://data.btcchina.com/data/ticker";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    if (!isset($json['ticker']['last'])) {
      throw new ExchangeRateException("Could not find any last rate");
    }

    $rate = array(
      "currency1" => "cny",
      "currency2" => "btc",
      "bid" => $json['ticker']['buy'],
      "ask" => $json['ticker']['sell'],
      "last_trade" => $json['ticker']['last'],
      "volume" => $json['ticker']['vol'],
      "high" => $json['ticker']['high'],
      "low" => $json['ticker']['low'],
      "vwap" => $json['ticker']['vwap'],
      "open" => $json['ticker']['open'],
    );

    return array($rate);
  }

}
