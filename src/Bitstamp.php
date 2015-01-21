<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class Bitstamp extends SimpleExchange {

  function getName() {
    return "Bitstamp";
  }

  function getCode() {
    return "bitstamp";
  }

  function getURL() {
    return "https://www.bitstamp.net/";
  }

  function fetchAllRates(Logger $logger) {
    $url = "https://www.bitstamp.net/api/ticker/";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    if (!isset($json['last'])) {
      throw new ExchangeRateException("Could not find any last rate");
    }

    $rate = array(
      "currency1" => "usd",
      "currency2" => "btc",
      "bid" => $json['bid'],
      "ask" => $json['ask'],
      "last_trade" => $json['last'],
      "volume" => $json['volume'],
      "high" => $json['high'],
      "low" => $json['low'],
      "vwap" => $json['vwap'],
    );

    return array($rate['currency1'] . $rate['currency2'] => $rate);
  }

}
