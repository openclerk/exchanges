<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Openclerk\Currencies\DisabledExchange;
use \Monolog\Logger;
use \Apis\Fetch;

class MintPal extends SimpleExchange implements DisabledExchange {

  function disabledAt() {
    return "2014-11-07";
  }

  function getName() {
    return "MintPal";
  }

  function getCode() {
    return "mintpal";
  }

  function getURL() {
    return "https://www.mintpal.com/";
  }

  public function fetchMarkets(Logger $logger) {
    return array(array('btc', 'dog'), array('btc', 'ltc'), array('btc', 'vtc'), array('btc', 'bc1'), array('btc', 'drk'),
        array('btc', 'vrc'));
  }

  function fetchAllRates(Logger $logger) {
    throw new ExchangeRateException("Cannot get rates of disabled exchange");
  }

}
