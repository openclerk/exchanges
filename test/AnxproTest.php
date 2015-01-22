<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the ANXPRO exchange.
 */
class AnxproTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\Anxpro());
  }

  function testHasUSDBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('usd', 'btc'), $markets), "Expected USD/BTC market in " . $this->printMarkets($markets));
  }

}
