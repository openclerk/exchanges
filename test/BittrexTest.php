<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the Bittrex exchange.
 */
class BittrexTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\Bittrex());
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
