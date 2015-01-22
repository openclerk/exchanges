<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the Coinbase exchange.
 */
class CoinbaseTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\Coinbase());
  }

  function testHasUSDBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'btc'), $markets), "Expected USD/BTC market in " . $this->printMarkets($markets));
  }

  function testHasGBPBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('gbp', 'btc'), $markets), "Expected GBP/BTC market in " . $this->printMarkets($markets));
  }

  function testHasNZDBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('nzd', 'btc'), $markets), "Expected NZD/BTC market in " . $this->printMarkets($markets));
  }

}
