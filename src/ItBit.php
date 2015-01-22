<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class ItBit extends SimpleExchange {

  function getName() {
    return "itBit";
  }

  function getCode() {
    return "itbit";
  }

  function getURL() {
    return "https://www.itbit.com/";
  }

  function getItBitCode($cur) {
    switch ($cur) {
      case "btc": return "XBT";

      default: return strtoupper($cur);
    }
  }

  /**
   * itBit does not have any API to list all available markets,
   * so we hardcode this.
   * Does not block.
   */
  public function fetchMarkets(Logger $logger) {
    return array(
      array('usd', 'btc'),
      array('eur', 'btc'),
      array('sgd', 'btc'),
    );
  }

  function fetchAllRates(Logger $logger) {
    $result = array();
    foreach ($this->fetchMarkets($logger) as $pair) {
      $this->throttle($logger);

      $currency1 = $pair[0];
      $currency2 = $pair[1];

      $key = $this->getItBitCode($currency2) . $this->getItBitCode($currency1);
      $url = "https://www.itbit.com/api/v2/markets/" . $key . "/orders";
      $logger->info($url);

      $orders = Fetch::jsonDecode(Fetch::get($url));

      if (isset($orders['message'])) {
        throw new ExternalAPIException($orders['message']);
      }
      if (!isset($orders['bids']) || count($orders['bids']) == 0) {
        throw new ExternalAPIException("No bids for $key");
      }
      if (!isset($orders['asks']) || count($orders['asks']) == 0) {
        throw new ExternalAPIException("No asks for $key");
      }

      $this->throttle($logger);

      $url = "https://www.itbit.com/api/v2/markets/" . $key . "/trades?since=0";
      $logger->info($url);

      // TODO keep track of last trade ID and use that as ?since parameter
      $trades = Fetch::jsonDecode(Fetch::get($url));

      $rate = array(
        "currency1" => $currency1,
        "currency2" => $currency2,
        "last_trade" => $trades[0]['price'],
        "bid" => $orders['bids'][0][0],
        "ask" => $orders['asks'][0][0],
      );

      $result[] = $rate;
    }

    return $result;
  }

}
