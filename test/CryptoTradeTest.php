<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the Crypto-Trade exchange.
 */
class CryptoTradeTest extends AbstractDisabledExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\CryptoTrade());
  }

  function disabled_testHasBTCLTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'ltc'), $markets), "Expected BTC/LTC market in " . $this->printMarkets($markets));
  }

  function disabled_testHasBTCDOG() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'dog'), $markets), "Expected BTC/DOG market in " . $this->printMarkets($markets));
  }

  function disabled_testHasLTCDOG() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('ltc', 'dog'), $markets), "Expected LTC/DOG market in " . $this->printMarkets($markets));
  }

}
