<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the BitmarketPl exchange.
 */
class BitmarketPlTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\BitmarketPl());
  }

  function testHasPLNBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('pln', 'btc'), $markets), "Expected PLN/BTC market in " . $this->printMarkets($markets));
  }

}
