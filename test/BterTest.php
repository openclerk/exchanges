<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the BTER exchange.
 */
class BterTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\Bter());
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

}
