<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the Vircurex exchange.
 */
class VircurexTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\Vircurex());
  }

  function testHasBTCLTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'ltc'), $markets), "Expected BTC/LTC market in " . $this->printMarkets($markets));
  }

  function testHasBTCDOG() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'dog'), $markets), "Expected BTC/DOG market in " . $this->printMarkets($markets));
  }

}
