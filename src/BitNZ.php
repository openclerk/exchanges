<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class BitNZ extends SimpleExchange {

  function getName() {
    return "BitNZ";
  }

  function getCode() {
    return "bitnz";
  }

  function getURL() {
    return "https://bitnz.com/";
  }

  function fetchAllRates(Logger $logger) {
    $url = "https://bitnz.com/api/0/ticker";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    if (!isset($json['last'])) {
      throw new ExchangeRateException("Could not find any last rate");
    }

    $rate = array(
      "currency1" => "nzd",
      "currency2" => "btc",
      "bid" => $json['bid'],
      "ask" => $json['ask'],
      "last_trade" => $json['last'],
      "volume" => $json['volume'],
      "high" => $json['high'],
      "low" => $json['low'],
      "vwap" => $json['vwap'],
    );

    return array($rate);
  }

}
