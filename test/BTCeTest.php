<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the BTCe exchange.
 */
class BTCeTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\BTCe());
  }

  function testHasUSDBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'btc'), $markets), "Expected USD/BTC market in " . $this->printMarkets($markets));
  }

  function testHasUSDGBP() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'gbp'), $markets), "Expected USD/GBP market in " . $this->printMarkets($markets));
  }

  function testHasPLNBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'ltc'), $markets), "Expected BTC/LTC market in " . $this->printMarkets($markets));
  }

}
