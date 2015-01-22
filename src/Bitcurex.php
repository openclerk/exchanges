<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class Bitcurex extends SimpleExchange {

  function getName() {
    return "Bitcurex";
  }

  function getCode() {
    return "bitcurex";
  }

  function getURL() {
    return "https://bitcurex.com/";
  }

  function getBitcurexCode($str) {
    return strtolower($str);
  }

  /**
   * Bitcurex does not have any API to list all available markets,
   * so we hardcode this.
   * Does not block.
   */
  public function fetchMarkets(Logger $logger) {
    return array(
      array('pln', 'btc'),
      array('eur', 'btc'),
      array('usd', 'btc'),
    );
  }

  function fetchAllRates(Logger $logger) {
    $result = array();
    foreach ($this->fetchMarkets($logger) as $pair) {
      $this->throttle($logger);

      $currency1 = $pair[0];
      $currency2 = $pair[1];

      $key = $this->getBitcurexCode($currency1);
      $url = "https://bitcurex.com/api/" . $key . "/ticker.json";
      $logger->info($url);

      $json = Fetch::jsonDecode(Fetch::get($url));

      if (!isset($json['last_tx_price'])) {
        throw new ExchangeRateException("Could not find any last rate");
      }

      $rate = array(
        "currency1" => $currency1,
        "currency2" => $currency2,
        "last_trade" => $json['last_tx_price'] / 1e4,
        "bid" => $json['best_bid'] / 1e4,
        "ask" => $json['best_ask'] / 1e4,
        "avg" => $json['average_price'] / 1e4,
        "volume" => $json['last24_volume'] / 1e8,
      );

      $result[$rate['currency1'] . $rate['currency2']] = $rate;
    }

    return $result;
  }

}
