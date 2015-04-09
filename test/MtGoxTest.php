<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the MtGox exchange.
 */
class MtGoxTest extends AbstractDisabledExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\MtGox());
  }

}
