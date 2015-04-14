<?php

namespace SecurityExchange\Tests;

use Monolog\Logger;

/**
 * Tests the {@link BTCT} exchange.
 */
class BTCTTest extends AbstractDisabledSecurityExchangeTest {

  function __construct() {
    parent::__construct(new \SecurityExchange\BTCT());
  }

}
