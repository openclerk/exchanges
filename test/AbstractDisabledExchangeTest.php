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
 * Abstracts away common test functionality,
 * for exchanges that are disabled.
 */
abstract class AbstractDisabledExchangeTest extends \PHPUnit_Framework_TestCase {

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

    Config::overwrite(array(
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

  function testExchangeCodeLength() {
    $this->assertGreaterThanOrEqual(1, strlen($this->exchange->getCode()));
    $this->assertLessThanOrEqual(32, strlen($this->exchange->getCode()));
  }

  /**
   * Overridden by {@link AbstractExchangeTest}.
   */
  function testNotDisabled() {
    $this->assertTrue($this->exchange instanceof DisabledExchange, "Expected this exchange to be disabled");
  }

}
