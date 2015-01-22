<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the Crypto-Trade exchange.
 */
class CryptoTradeTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\CryptoTrade());
  }

  function testHasBTCLTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'ltc'), $markets), "Expected BTC/LTC market in " . $this->printMarkets($markets));
  }

  function testHasBTCDOG() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'dog'), $markets), "Expected BTC/DOG market in " . $this->printMarkets($markets));
  }

  function testHasLTCDOG() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('ltc', 'dog'), $markets), "Expected LTC/DOG market in " . $this->printMarkets($markets));
  }

}
