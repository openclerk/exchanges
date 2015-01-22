<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the Bit2c exchange.
 */
class Bit2cTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\Bit2c());
    $this->logger->info("AnxproTest::Construct");
  }

  function testHasILSBTCMarket() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('ils', 'btc'), $markets), "Expected ILS/BTC market in " . $this->printMarkets($markets));
  }

}
