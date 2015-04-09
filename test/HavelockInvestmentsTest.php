<?php

namespace SecurityExchange\Tests;

use Monolog\Logger;

/**
 * Tests the {@link HavelockInvestments} exchange.
 */
class HavelockInvestmentsTest extends AbstractSecurityExchangeTest {

  function __construct() {
    parent::__construct(new \SecurityExchange\HavelockInvestments());
  }

  function testHasAM1() {
    $markets = $this->getSecurities();
    $this->assertNotFalse(array_search(array('currency' => 'btc', 'security' => 'AM1'), $markets), "Expected AM1 BTC market in " . $this->printSecurities($markets));
  }

  function testHasAM100() {
    $markets = $this->getSecurities();
    $this->assertNotFalse(array_search(array('currency' => 'btc', 'security' => 'AM100'), $markets), "Expected AM100 BTC market in " . $this->printSecurities($markets));
  }

  function testAM1Name() {
    $this->assertEquals("ASICMINER Full Shares", $this->exchange->fetchName("AM1", $this->logger));
  }

}
