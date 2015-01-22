<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the itBit exchange.
 */
class ItBitTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\ItBit());
  }

  function testHasUSDBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'btc'), $markets), "Expected USD/BTC market in " . $this->printMarkets($markets));
  }

  function testHasEURBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('eur', 'btc'), $markets), "Expected EUR/BTC market in " . $this->printMarkets($markets));
  }

}
