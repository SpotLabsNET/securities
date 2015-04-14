<?php

namespace SecurityExchange\Account;

use \Monolog\Logger;
use \Apis\Fetch;
use \Account\SimpleAccountType;
use \Account\SecurityExchangeAccountType;

/**
 * Simplifies the implementation of accounts on security exchanges, which
 * are also associated with a given {@link SecurityExchange}.
 */
abstract class AbstractSecurityExchangeAccountType extends SimpleAccountType implements SecurityExchangeAccountType {

  /**
   * Get the associated {@link SecurityExchange} for this account
   * @return a {@link SecurityExchange} that can be used to obtain additional information
   */
  abstract function getSecurityExchange();

  function getName() {
    return $this->getSecurityExchange()->getName();
  }

  function getCode() {
    return $this->getSecurityExchange()->getCode();
  }

  function getURL() {
    return $this->getSecurityExchange()->getURL();
  }

  /**
   * Helper function to get all securities value for the given security for this account,
   * or {@code null} if there is no balance for this security.
   * May block.
   *
   * @param $account fields that satisfy {@link #getFields()}
   * @return array('owned', 'available', 'reserved', ...) or {@code null}
   */
  public function fetchSecurity($security, $account, Logger $logger) {
    $securities = $this->fetchSecurities($account, $logger);
    if (isset($securities[$security])) {
      return $securities[$security];
    } else {
      return null;
    }
  }

  /**
   * Helper function to get the current, owned number of shares (units) of the given security
   * for this account.
   * May block.
   *
   * @param $account fields that satisfy {@link #getFields()}
   */
  public function fetchSecurityBalance($security, $account, Logger $logger) {
    $securities = $this->fetchSecurities($account, $logger);
    if (isset($securities[$security]) && isset($securities[$security]['owned'])) {
      return $securities[$security]['owned'];
    } else {
      return null;
    }
  }

}
