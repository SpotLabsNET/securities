<?php

namespace SecurityExchange\Account;

use \Monolog\Logger;
use \Apis\Fetch;
use \Account\AccountFetchException;
use \Apis\FetchHttpException;
use \Apis\FetchException;
use \Openclerk\Currencies\CurrencyFactory;

/**
 * Represents the Havelock Investments security exchange accounts.
 */
class HavelockInvestments extends AbstractSecurityExchangeAccountType {

  public function getSecurityExchange() {
    return new \SecurityExchange\HavelockInvestments();
  }

  public function getFields() {
    return array(
      'api_key' => array(
        'title' => "API Key",
        'regexp' => '#^[0-9A-Za-z]{64}$#',
      ),
    );
  }

  public function fetchSupportedCurrencies(CurrencyFactory $factory, Logger $logger) {
    // only supports btc
    return array('btc');
  }

  protected function havelockQuery($url, $post_data, Logger $logger) {

    $logger->info($url);

    try {
      $this->throttle($logger);
      $raw = Fetch::post($url, $post_data);
    } catch (FetchHttpException $e) {
      throw new AccountFetchException($e->getMessage(), $e);
    }

    if ($raw === "Access denied") {
      throw new AccountFetchException($raw);
    }

    $json = Fetch::jsonDecode($raw);
    if (isset($json['message']) && $json['message']) {
      throw new AccountFetchException($json['message']);
    }
    if (is_array($json) && !$json) {
      throw new AccountFetchException("Havelock API returned an empty array");
    }

    return $json;

  }

  /**
   * @return all account balances
   * @throws AccountFetchException if something bad happened
   */
  public function fetchBalances($account, CurrencyFactory $factory, Logger $logger) {

    $json = $this->havelockQuery("https://www.havelockinvestments.com/r/balance", array('key' => $account['api_key']), $logger);

    $result = array(
      'btc' => array(
        'confirmed' => $json['balance']['balance'],
        'reserved' => $json['balance']['balanceescrow'],
        'available' => $json['balance']['balanceavailable'],
      ),
    );

    return $result;

  }

  /**
   * Get all securities balances for this account.
   *
   * @param $account fields that satisfy {@link #getFields()}
   * @return an array of ('security' => ('owned', 'available', 'reserved', ...))
   */
  public function fetchSecurities($account, Logger $logger) {

    $json = $this->havelockQuery("https://www.havelockinvestments.com/r/portfolio", array('key' => $account['api_key']), $logger);

    $result = array();
    if (isset($json['portfolio'])) {
      foreach ($json['portfolio'] as $security) {
        $result[$security['symbol']] = array(
          'owned' => $security['quantity'],
          'available' => $security['quantity'] - $security['quantityescrow'],
          'reserved' => $security['quantityescrow'],
          'value' => $security['marketvalue'],
        );
      }
    }

    return $result;

  }

}
