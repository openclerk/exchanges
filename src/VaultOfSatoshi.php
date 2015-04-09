<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Openclerk\Currencies\DisabledExchange;
use \Monolog\Logger;
use \Apis\Fetch;

class VaultOfSatoshi extends SimpleExchange implements DisabledExchange {

  function disabledAt() {
    return "2014-11-07";
  }

  function getName() {
    return "Vault of Satoshi";
  }

  function getCode() {
    return "vaultofsatoshi";
  }

  function getURL() {
    return "https://www.vaultofsatoshi.com/";
  }

  public function fetchMarkets(Logger $logger) {
    throw new ExchangeRateException("Cannot get markets of disabled exchange");
  }

  function fetchAllRates(Logger $logger) {
    throw new ExchangeRateException("Cannot get rates of disabled exchange");
  }

}
