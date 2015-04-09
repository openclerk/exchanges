<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Openclerk\Currencies\DisabledExchange;
use \Monolog\Logger;
use \Apis\Fetch;

class MtGox extends SimpleExchange implements DisabledExchange {

  function disabledAt() {
    return "2014-11-07";
  }

  function getName() {
    return "Mt.Gox";
  }

  function getCode() {
    return "mtgox";
  }

  function getURL() {
    return "https://mtgox.com/";
  }

  public function fetchMarkets(Logger $logger) {
    return array();
  }

  function fetchAllRates(Logger $logger) {
    throw new ExchangeRateException("Cannot get rates of disabled exchange");
  }

}
