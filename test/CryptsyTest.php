<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the Cryptsy exchange.
 */
class CryptsyTest extends AbstractExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\Cryptsy());

    // the Cryptsy API is really large so we need to wait longer for it to download
    \Openclerk\Config::overwrite(array(
      "get_contents_timeout" => 30,
    ));
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
   * We instead test a single market that should always have valid values
   */
  function testBTCLTCAskHigherThanBid() {
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
