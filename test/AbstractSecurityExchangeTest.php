<?php

namespace SecurityExchange\Tests;

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
abstract class AbstractSecurityExchangeTest extends AbstractDisabledSecurityExchangeTest {

  function printSecurities($securities) {
    $result = array();
    foreach ($securities as $market) {
      $result[] = implode("/", $market);
    }
    return "[" . implode(", ", $result) . "]";
  }

  /**
   * Calls {@link Exchange#fetchSecurities()} but caches the return value so that we don't
   * spam services when testing.
   */
  function getSecurities() {
    if (!isset(self::$securities[$this->exchange->getCode()])) {
      try {
        self::$securities[$this->exchange->getCode()] = $this->exchange->fetchSecurities($this->logger);
        $this->logger->info("Found " . $this->printSecurities(self::$securities[$this->exchange->getCode()]) . " securities");
      } catch (\Api\FetchException $e) {
        // don't continually request the same failing security exchange multiple times
        self::$rates[$this->exchange->getCode()] = false;
        throw $e;
      }
    }
    return self::$securities[$this->exchange->getCode()];
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

  function testHasAtLeastOneSecurity() {
    $securities = $this->getSecurities();
    $this->assertGreaterThan(0, count($securities), "Expected at least one security");
  }

  function testAllSecuritiesHaveLastTrade() {
    $rates = $this->getAllRates();
    foreach ($rates as $rate) {
      $key = $rate['currency'] . "/" . $rate['security'];
      $this->assertTrue(isset($rate['last_trade']), "last_trade not set in " . print_r($rate, true));
      $this->assertGreaterThan(0, $rate['last_trade'], "Last trade for '$key' should be greater than 0");
    }
  }

  function testAllRatesProvideCurrencyCodes() {
    $rates = $this->getAllRates();
    foreach ($rates as $rate) {
      $this->assertTrue(isset($rate['currency']), "currency should be set in  " . print_r($rate, true));
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
          $key = $rate['currency'] . "/" . $rate['security'];
          $this->assertGreaterThanOrEqual($rate['bid'], $rate['ask'], "Expected ask > bid for '$key' market");
        }
      }
    }
  }

  /**
   * For all markets, the high should always be higher than the low - or else there is
   * something odd going on.
   */
  function testAllMarketsHaveHighHigherThanLow() {
    $rates = $this->getAllRates();
    foreach ($rates as $rate) {
      if (isset($rate['high']) && isset($rate['low'])) {
        // some exchanges have markets with zero bids or zero asks; don't fail here
        if ($rate['high'] != 0 && $rate['low'] != 0) {
          $key = $rate['currency'] . "/" . $rate['security'];
          $this->assertGreaterThanOrEqual($rate['low'], $rate['high'], "Expected high > low for '$key' market");
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
