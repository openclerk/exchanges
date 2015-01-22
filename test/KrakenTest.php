<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the Kraken exchange.
 */
class KrakenTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\Kraken());
  }

  function testHasUSDBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'btc'), $markets), "Expected USD/BTC market in " . $this->printMarkets($markets));
  }

  function testHasEURBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('eur', 'btc'), $markets), "Expected EUR/BTC market in " . $this->printMarkets($markets));
  }

  function testHasBTCLTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'ltc'), $markets), "Expected BTC/LTC market in " . $this->printMarkets($markets));
  }

}
