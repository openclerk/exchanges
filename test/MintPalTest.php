<?php

namespace Exchange\Tests;

use Monolog\Logger;

/**
 * Tests the {@link MintPal} exchange.
 */
class MintPalTest extends AbstractDisabledExchangeTest {

  function __construct() {
    parent::__construct(new \Exchange\MintPal());
  }

}
