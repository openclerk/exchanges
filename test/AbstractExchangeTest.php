<?php

namespace Exchange\Tests;

use Monolog\Logger;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\ErrorLogHandler;
use Openclerk\Config;
use Openclerk\Currencies\Exchange;
use Openclerk\Currencies\DisabledExchange;

/**
 * Abstracts away common test functionality.
 */
abstract class AbstractExchangeTest extends AbstractDisabledExchangeTest {

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
      try {
        self::$markets[$this->exchange->getCode()] = $this->exchange->fetchMarkets($this->logger);
        $this->logger->info("Found " . $this->printMarkets(self::$markets[$this->exchange->getCode()]) . " markets");
      } catch (\Api\FetchException $e) {
        // don't continually request the same failing exchange multiple times
        self::$rates[$this->exchange->getCode()] = false;
        throw $e;
      }
    }
    return self::$markets[$this->exchange->getCode()];
  }

  /**
   * Calls {@link Exchange#fetchAllRates()} but caches the return value so that we don't
   * spam services when testing.
   */
  function getAllRates() {
    if (!isset(self::$rates[$this->exchange->getCode()])) {
      try {
        self::$rates[$this->exchange->getCode()] = $this->exchange->fetchAllRates($this->logger);
      } catch (\Api\FetchException $e) {
        // don't continually request the same failing exchange multiple times
        self::$rates[$this->exchange->getCode()] = false;
        throw $e;
      }
    }
    return self::$rates[$this->exchange->getCode()];
  }

  function testHasAtLeastOneMarket() {
    $markets = $this->getAllMarkets();
    $this->assertGreaterThan(0, count($markets), "Expected at least one market");
  }

  function testAllMarketsHaveLastTrade() {
    $rates = $this->getAllRates();
    foreach ($rates as $rate) {
      $key = $rate['currency1'] . "/" . $rate['currency2'];
      $this->assertTrue(isset($rate['last_trade']), "last_trade not set in " . print_r($rate, true));
      $this->assertGreaterThan(0, $rate['last_trade'], "Last trade for '$key' should be greater than 0");
    }
  }

  function testAllRatesProvideCurrencyCodes() {
    $rates = $this->getAllRates();
    foreach ($rates as $rate) {
      $this->assertTrue(isset($rate['currency1']), "currency1 should be set in  " . print_r($rate, true));
      $this->assertTrue(isset($rate['currency2']), "currency2 should be set in " . print_r($rate, true));
    }
  }

  /**
   * For all markets, the ask should always be higher than the bid - or else there is
   * something odd going on.
   *
   * The 'bid' price is the highest price that a buyer is willing to pay (i.e. the 'sell');
   * the 'ask' price is the lowest price that a seller is willing to sell (i.e. the 'buy').
   * Therefore bid <= ask, buy <= sell.
   */
  function testAllMarketsHaveAskHigherThanBid() {
    $rates = $this->getAllRates();
    foreach ($rates as $rate) {
      if (isset($rate['ask']) && isset($rate['bid'])) {
        // some exchanges have markets with zero bids or zero asks; don't fail here
        if ($rate['ask'] != 0 && $rate['bid'] != 0) {
          $key = $rate['currency1'] . "/" . $rate['currency2'];
          $this->assertGreaterThanOrEqual($rate['bid'], $rate['ask'], "Expected ask > bid for '$key' market");
        }
      }
    }
  }

  /**
   * In openclerk/exchanges, we want to return all exchange pairs according to a
   * particular currency order.
   */
  function testMarketsAreOrdered() {
    $markets = $this->getAllMarkets();
    foreach ($markets as $pair) {
      if (\Exchange\CurrencyOrder::hasOrder($pair[0])) {
        if (\Exchange\CurrencyOrder::hasOrder($pair[1])) {
          $this->assertTrue(\Exchange\CurrencyOrder::isOrdered($pair[0], $pair[1]), "Expected reverse order of pair " . implode("/", $pair));
        }
      }
    }
  }

  /**
   * In openclerk/exchanges, we want to return all exchange pairs according to a
   * particular currency order.
   */
  function testRatesAreOrdered() {
    $rates = $this->getAllRates();
    foreach ($rates as $rate) {
      if (\Exchange\CurrencyOrder::hasOrder($rate['currency1'])) {
        if (\Exchange\CurrencyOrder::hasOrder($rate['currency2'])) {
          $this->assertTrue(\Exchange\CurrencyOrder::isOrdered($rate['currency1'], $rate['currency2']), "Expected reverse order of pair " . $rate['currency1'] . "/" . $rate['currency2']);
        }
      }
    }
  }

  /**
   * @override
   */
  function testNotDisabled() {
    $this->assertFalse($this->exchange instanceof DisabledExchange, "We cannot run tests on disabled exchanges");
  }

}
