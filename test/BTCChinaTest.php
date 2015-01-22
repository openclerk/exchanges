<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the BTCChina exchange.
 */
class BTCChinaTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\BTCChina());
  }

  function testHasCNYBTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('cny', 'btc'), $markets), "Expected CNY/BTC market in " . $this->printMarkets($markets));
  }

}
