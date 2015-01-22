<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the Bitstamp exchange.
 */
class BitstampTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\Bitstamp());
  }

  function testHasUSDBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'btc'), $markets), "Expected USD/BTC market in " . $this->printMarkets($markets));
  }

}
