<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the TheMoneyConverter exchange.
 */
class TheMoneyConverterTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\TheMoneyConverter());
  }

  function testHasUSDNZD() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'nzd'), $markets), "Expected USD/NZD market in " . $this->printMarkets($markets));
  }

  function testHasUSDGBP() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'gbp'), $markets), "Expected USD/GBP market in " . $this->printMarkets($markets));
  }

  /**
   * Tests that we get the correct values for a given pair.
   * usd/nzd means "usd per nzd" therefore should be < 1.0.
   * Issue #423
   */
  function testUSDNZDLastTradeValue() {
    $last_trade = $this->exchange->fetchLastTrade('usd', 'nzd', $this->logger);
    $this->assertNotNull($last_trade);
    $this->assertLessThan(1.0, $last_trade);
  }

}
