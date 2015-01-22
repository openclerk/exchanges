<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the Bitcurex exchange.
 */
class BitcurexTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\Bitcurex());
  }

  function testHasUSDBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'btc'), $markets), "Expected USD/BTC market in " . $this->printMarkets($markets));
  }

  function testHasPLNBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('pln', 'btc'), $markets), "Expected PLN/BTC market in " . $this->printMarkets($markets));
  }

}
