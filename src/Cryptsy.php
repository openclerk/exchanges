<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class Cryptsy extends SimpleExchange {

  function getName() {
    return "Cryptsy";
  }

  function getCode() {
    return "cryptsy";
  }

  function getURL() {
    return "https://www.cryptsy.com/";
  }

  /**
   * Convert the given Cryptsy currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    switch (strtoupper($str)) {
      // exceptions
      case "DOGE": return "dog";
      case "BC": return "bc1";

      // otherwise return lowercase
      default:
        return strtolower($str);
    }
  }

  function fetchAllRates(Logger $logger) {
    // this is a huge file!
    $url = "http://pubapi.cryptsy.com/api.php?method=marketdatav2";
    $logger->info($url);

    $raw = Fetch::get($url);

    // reduce the size of the JSON file to reduce memory usage
    $raw = preg_replace('#,"recenttrades":\\[.+?\\]#', "", $raw);

    $json = Fetch::jsonDecode($raw);

    if (!$json['success']) {
      throw new ExchangeRateException("API request failed");
    }

    $result = array();

    foreach ($json['return']['markets'] as $key => $market) {
      if ($market['lasttradeprice'] == 0) {
        $logger->info("Ignoring '$key' market: last trade price is 0");
        continue;
      }

      $currency1 = $this->getCurrencyCode($market['secondarycode']);
      $currency2 = $this->getCurrencyCode($market['primarycode']);

      $rate = array(
        "currency1" => $currency1,
        "currency2" => $currency2,
        "last_trade" => $market['lasttradeprice'],
        "volume" => $market['volume'],
        // Cryptsy returns buy/sell incorrectly
        'bid' => $market['buyorders'][0]['price'],
        'ask' => $market['sellorders'][0]['price'],
      );

      $result[] = $rate;
    }

    return $result;
  }

}
