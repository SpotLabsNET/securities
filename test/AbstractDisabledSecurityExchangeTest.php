<?php

namespace SecurityExchange\Tests;

use Monolog\Logger;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\ErrorLogHandler;
use Openclerk\Config;
use Openclerk\Currencies\SecurityExchange;
use Openclerk\Currencies\DisabledExchange;

/**
 * Abstracts away common test functionality,
 * for exchanges that are disabled.
 */
abstract class AbstractDisabledSecurityExchangeTest extends \PHPUnit_Framework_TestCase {

  // we cache securities and rate values so we don't spam services
  static $securities = array();
  static $rates = array();

  function __construct(SecurityExchange $exchange) {
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
   * Overridden by {@link AbstractSecuritiesExchangeTest}.
   */
  function testNotDisabled() {
    $this->assertTrue($this->exchange instanceof DisabledExchange, "Expected this security exchange to be disabled");
  }

  /**
   * Return a file path to {@code __DIR__ . "/../security-exchanges.json"}, by default.
   */
  function getSecurityExchangesJSON() {
    return __DIR__ . "/../security-exchanges.json";
  }

  static $tested_codes = array();

  function testUniqueCode() {
    $code = $this->exchange->getCode();
    $this->assertFalse(isset(self::$tested_codes[$code]), "We've already tested an security exchange '$code'");
    self::$tested_codes[$code] = $code;
  }

  function testCodeInAccountsJson() {
    $this->assertFileExists($this->getSecurityExchangesJSON());
    $json = json_decode(file_get_contents($this->getSecurityExchangesJSON()), true /* assoc */);
    $code = $this->exchange->getCode();
    $this->assertTrue(isset($json[$code]), "Expected '$code' account in securities-exchanges.json");
    $this->assertEquals("\\" . get_class($this->exchange), $json[$code], "Expected '$code' to return the same class");
  }

}
