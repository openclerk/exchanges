<?php

namespace Exchange\Tests;

use Exchange\CurrencyOrder;

/**
 * Tests the CurrencyOrder.
 */
class CurrencyOrderTest extends \PHPUnit_Framework_TestCase {

  function testUSDBTC() {
    $this->assertTrue(CurrencyOrder::isOrdered("usd", "btc"));
    $this->assertFalse(CurrencyOrder::isOrdered("btc", "usd"));
  }

  function testUSDLTC() {
    $this->assertTrue(CurrencyOrder::isOrdered("usd", "ltc"));
    $this->assertFalse(CurrencyOrder::isOrdered("ltc", "usd"));
  }

  function testBTCLTC() {
    $this->assertTrue(CurrencyOrder::isOrdered("btc", "ltc"));
    $this->assertFalse(CurrencyOrder::isOrdered("ltc", "btc"));
  }

}
