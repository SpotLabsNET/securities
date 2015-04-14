<?php

namespace SecurityExchange\Account;

use \Monolog\Logger;
use \Apis\Fetch;
use \Account\AccountFetchException;
use \Account\DisabledAccount;
use \Apis\FetchHttpException;
use \Apis\FetchException;
use \Openclerk\Currencies\CurrencyFactory;

/**
 * Represents the BTCT security exchange accounts.
 */
class BTCT extends AbstractSecurityExchangeAccountType implements DisabledAccount {

  public function getSecurityExchange() {
    return new \SecurityExchange\BTCT();
  }

  function disabledAt() {
    return $this->getSecurityExchange()->disabledAt();
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[a-f0-9]{64}$#',
      ),
    );
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    // only supports btc
    return array('btc');
  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {
    throw new AccountFetchException("Cannot fetch balances of a disabled account");
  }

  /**
   * Get all securities balances for this account.
   *
   * @param $account fields that satisfy {@link #getFields()}
   * @return an array of ('security' => ('owned', 'available', 'reserved', ...))
   */
  public function fetchSecurities($account, Logger $logger) {
    throw new AccountFetchException("Cannot fetch securities of a disabled account");
  }

}
