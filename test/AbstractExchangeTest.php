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
  static $markets = array();
  static $rates = array();

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

  /**
   * Calls {@link Exchange#fetchMarkets()} but caches the return value so that we don't
   * spam services when testing.
   */
  function getAllMarkets() {
    if (!isset(self::$markets[$this->exchange->getCode()])) {
      self::$markets[$this->exchange->getCode()] = $this->exchange->fetchMarkets($this->logger);
    }
    return self::$markets[$this->exchange->getCode()];
  }

  /**
   * Calls {@link Exchange#fetchAllRates()} but caches the return value so that we don't
   * spam services when testing.
   */
  function getAllRates() {
    if (!isset(self::$rates[$this->exchange->getCode()])) {
      self::$rates[$this->exchange->getCode()] = $this->exchange->fetchAllRates($this->logger);
    }
    return self::$rates[$this->exchange->getCode()];
  }

  function testExchangeCodeLength() {
    $this->assertGreaterThanOrEqual(1, strlen($this->exchange->getCode()));
    $this->assertLessThanOrEqual(32, strlen($this->exchange->getCode()));
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

  function testAllRatesProvideCurrencyCodes() {
    $rates = $this->getAllRates();
    foreach ($rates as $key => $rate) {
      $this->assertTrue(isset($rate['currency1']), "currency1 should be set in " . print_r($rate, true));
      $this->assertTrue(isset($rate['currency2']), "currency2 should be set in " . print_r($rate, true));
      $currency1 = substr($key, 0, 3);
      $currency2 = substr($key, 3, 3);
      $this->assertEquals($currency1, $rate['currency1'], "currency1 was not '$currency1' from key '$key'");
      $this->assertEquals($currency2, $rate['currency2'], "currency2 was not '$currency2' from key '$key'");
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
