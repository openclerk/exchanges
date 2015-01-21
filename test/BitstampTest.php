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
    $rates = $this->exchange->fetchMarkets($this->logger);
    $this->assertNotFalse(array_search(array('usd', 'btc'), $rates), "Expected USD/BTC market in " . $this->printMarkets($rates));
  }


  function testHasAtLeastOneMarket() {
    $rates = $this->exchange->fetchMarkets($this->logger);
    $this->assertGreaterThan(0, count($rates), "Expected at least one market");
  }

}
