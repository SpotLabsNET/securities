<?php

namespace SecurityExchange\Tests\Account;

use Monolog\Logger;
use Openclerk\Config;
use Openclerk\Currencies\Currency;

/**
 * Tests the {@link BTCT} account type.
 */
class BTCTTest extends AbstractDisabledSecurityExchangeTest {

  function __construct() {
    parent::__construct(new \SecurityExchange\Account\BTCT());
  }

}
