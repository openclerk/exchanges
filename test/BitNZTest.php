<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the BitNZ exchange.
 */
class BitNZTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\BitNZ());
  }

  function testHasNZDBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('nzd', 'btc'), $markets), "Expected NZD/BTC market in " . $this->printMarkets($markets));
  }

}
