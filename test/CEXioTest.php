<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the CEX.io exchange.
 */
class CEXioTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\CEXio());
  }

  function testHasUSDBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'btc'), $markets), "Expected USD/BTC market in " . $this->printMarkets($markets));
  }

  function testHasUSDLTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'ltc'), $markets), "Expected USD/LTC market in " . $this->printMarkets($markets));
  }

  function testHasBTCLTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'ltc'), $markets), "Expected BTC/LTC market in " . $this->printMarkets($markets));
  }

  function testHasBTCGHS() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'ghs'), $markets), "Expected BTC/GHS market in " . $this->printMarkets($markets));
  }

  function testHasUSDGHS() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'ghs'), $markets), "Expected USD/GHS market in " . $this->printMarkets($markets));
  }

}
