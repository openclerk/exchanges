<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the VirtEx exchange.
 */
class VirtExTest extends AbstractDisabledExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\VirtEx());
  }

  function disabled_testHasUSDBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'btc'), $markets), "Expected USD/BTC market in " . $this->printMarkets($markets));
  }

  function disabled_testHasUSDLTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'ltc'), $markets), "Expected USD/LTC market in " . $this->printMarkets($markets));
  }

  function disabled_testHasEURBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('eur', 'btc'), $markets), "Expected EUR/BTC market in " . $this->printMarkets($markets));
  }

  function disabled_testHasEURLTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('eur', 'ltc'), $markets), "Expected EUR/LTC market in " . $this->printMarkets($markets));
  }

}
