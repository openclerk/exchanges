<?php

namespace Exchange\Tests;

use Monolog\Logger;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\ErrorLogHandler;
use Openclerk\Config;
use Openclerk\Currencies\Exchange;

/**
 * Abstracts away common test functionality.
 */
abstract class AbstractExchangeTest extends \PHPUnit_Framework_TestCase {

  // we cache market and rate values so we don't spam services
  var $markets = null;
  var $rates = null;

  function __construct(Exchange $exchange) {
    $this->logger = new Logger("test");
    $this->exchange = $exchange;

    if ($this->isDebug()) {
      $this->logger->pushHandler(new BufferHandler(new ErrorLogHandler()));
    } else {
      $this->logger->pushHandler(new NullHandler());
    }

    Config::merge(array(
      "get_contents_timeout" => 10,
    ));
  }

  function isDebug() {
    global $argv;
    if (isset($argv)) {
      foreach ($argv as $value) {
        if ($value === "--debug" || $value === "--verbose") {
          return true;
        }
      }
    }
    return false;
  }

  function printMarkets($markets) {
    $result = array();
    foreach ($markets as $market) {
      $result[] = implode("/", $market);
    }
    return "[" . implode(", ", $result) . "]";
  }

  function getAllMarkets() {
    if ($this->markets === null) {
      $this->markets = $this->exchange->fetchMarkets($this->logger);
    }
    return $this->markets;
  }

  function getAllRates() {
    if ($this->rates === null) {
      $this->rates = $this->exchange->fetchAllRates($this->logger);
    }
    return $this->rates;
  }

  function testHasAtLeastOneMarket() {
    $markets = $this->getAllMarkets();
    $this->assertGreaterThan(0, count($markets), "Expected at least one market");
  }

  function testAllMarketsHaveLastTrade() {
    $rates = $this->getAllRates();
    foreach ($rates as $key => $rate) {
      $this->assertTrue(isset($rate['last_trade']), "last_trade not set in " . print_r($rate, true));
      $this->assertGreaterThan(0, $rate['last_trade'], "Last trade for '$key' should be greater than 0");
    }
  }

  /**
   * For all markets, the bid should always be higher than the ask - or else there is
   * something odd going on.
   */
  function testAllMarketsHaveBidHigherThanAsk() {
    $rates = $this->getAllRates();
    foreach ($rates as $key => $rate) {
      if (isset($rate['bid']) && isset($rate['ask'])) {
        $this->assertGreaterThanOrEqual($rate['bid'], $rate['ask'], "Expected bid > ask for '$key' market");
      }
    }
  }

}
