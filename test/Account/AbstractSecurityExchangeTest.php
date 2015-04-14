<?php

namespace SecurityExchange\Tests\Account;

use Monolog\Logger;
use Account\AccountType;
use Account\AccountFetchException;
use Account\Tests\AbstractActiveAccountTest;
use Openclerk\Config;
use Openclerk\Currencies\Currency;

/**
 * Abstracts away common test functionality.
 */
abstract class AbstractSecurityExchangeTest extends AbstractActiveAccountTest {

  public function __construct(AccountType $type) {
    parent::__construct($type);
    Config::merge(array(
      // reduce throttle time for tests
      "accounts_throttle" => 1,
    ));
  }

  /**
   * In openclerk/wallets, extend this to return instances of openclerk/cryptocurrencies
   */
  function loadCurrency($cur) {
    switch ($cur) {
      case "dog":
        return new \Cryptocurrency\Dogecoin();

      default:
        return null;
    }
  }

  function getAccountsJSON() {
    return __DIR__ . "/../../accounts.json";
  }

  function testValidSecurities() {
    $account = $this->getValidAccount();
    $securities = $this->account->fetchSecurities($account, $this->logger);
    $this->doTestValidSecurities($securities);
  }

  /**
   * Do tests as appropriate.
   */
  abstract function doTestValidSecurities($balances);

  function testMissingSecurities() {
    $account = $this->getMissingAccount();
    try {
      $balances = $this->account->fetchSecurities($account, $this->logger);
      $this->fail("Expected an AccountFetchException");
    } catch (AccountFetchException $e) {
      // expected
      $this->assertGreaterThan(0, strlen($e->getMessage()), "Expected missing account to return an error message");
    }
  }

  function testInvalidSecurities() {
    $account = $this->getInvalidAccount();
    try {
      $balances = $this->account->fetchSecurities($account, $this->logger);
      $this->fail("Expected an AccountFetchException");
    } catch (AccountFetchException $e) {
      // expected
      $this->assertGreaterThan(0, strlen($e->getMessage()), "Expected an invalid account to return an error message");
    }
  }

}
