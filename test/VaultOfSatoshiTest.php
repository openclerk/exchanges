<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the {@link VaultOfSatoshi} exchange.
 */
class VaultOfSatoshiTest extends AbstractDisabledExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\VaultOfSatoshi());
  }

}
