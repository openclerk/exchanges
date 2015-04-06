<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class Bittrex extends SimpleExchange {

  function getName() {
    return "Bittrex";
  }

  function getCode() {
    return "bittrex";
  }

  function getURL() {
    return "https://bittrex.com/";
  }

  /**
   * Convert the given Bittrex currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    switch (strtoupper($str)) {
      // exceptions
      case "DOGE": return "dog";
      case "BLK": return "bc1";

      // otherwise return lowercase
      default:
        return strtolower($str);
    }
  }

  function fetchAllRates(Logger $logger) {
    $url = "https://bittrex.com/api/v1.1/public/getmarketsummaries";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    if (!$json['success']) {
      throw new ExchangeRateException($json['message']);
    }

    $result = array();

    foreach ($json['result'] as $market) {
      $pairs = explode("-", $market['MarketName'], 2);

      $currency1 = $this->getCurrencyCode($pairs[0]);
      $currency2 = $this->getCurrencyCode($pairs[1]);

      $rate = array(
        "currency1" => $currency1,
        "currency2" => $currency2,
        "bid" => $market['Bid'],
        "ask" => $market['Ask'],
        "last_trade" => $market['Last'],
        "volume" => $market['BaseVolume'],
        "high" => $market['High'],
        "low" => $market['Low'],
      );

      $result[] = $rate;
    }

    return $result;
  }

}
