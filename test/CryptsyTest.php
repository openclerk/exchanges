<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the Cryptsy exchange.
 */
class CryptsyTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\Cryptsy());
  }

  function testHasBTCLTC() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'ltc'), $markets), "Expected BTC/LTC market in " . $this->printMarkets($markets));
  }

  function testHasBTCDOG() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('btc', 'dog'), $markets), "Expected BTC/DOG market in " . $this->printMarkets($markets));
  }

  function testHasLTCDOG() {
    $markets = $this->getAllMarkets();
    $this->assertNotFalse(array_search(array('ltc', 'dog'), $markets), "Expected LTC/DOG market in " . $this->printMarkets($markets));
  }

  /**
   * We ignore this test because the Crypsty API does not always return correct values
   * (i.e. for some markets, bid *will* be higher than ask)
   */
  function testAllMarketsHaveAskHigherThanBid() {
    // empty
  }

  /**
   * We instead test a single market that should always have valid values.
   * This is now disabled because we no longer have access to bid/ask prices directly
   */
  function disabled_testBTCLTCAskHigherThanBid() {
    $rates = $this->getAllRates();
    foreach ($rates as $rate) {
      if ($rate['currency1'] == "btc" && $rate['currency2'] == "ltc") {
        $this->assertGreaterThanOrEqual($rate['bid'], $rate['ask'], "Expected ask > bid for BTC/LTC market");
        return;
      }
    }

    $this->fail("Expected to find BTC/LTC market");
  }

}
