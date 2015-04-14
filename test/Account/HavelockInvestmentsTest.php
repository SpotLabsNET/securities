<?php

namespace SecurityExchange\Tests\Account;

use Monolog\Logger;
use Openclerk\Config;
use Openclerk\Currencies\Currency;

/**
 * Tests the {@link HavelockInvestments} account type.
 */
class HavelockInvestmentsTest extends AbstractSecurityExchangeTest {

  function __construct() {
    parent::__construct(new \SecurityExchange\Account\HavelockInvestments());
  }

  /**
   * Get some field values for a valid account.
   * @return array of fields
   */
  function getValidAccount() {
    return array(
      'api_key' => 'VJzEKb4F6Hxzx3nyub4NDtyezE8gAk9Vvza6SUCPczESstGcMArQufwjkuJmWH9H',
    );
  }

  /**
   * Get some field values for a missing account,
   * but one that is still valid according to the fields.
   * @return array of fields
   */
  function getMissingAccount() {
    return array(
      'api_key' => 'VJzEKb4F6Hxzx3nyub4NDtyezE8gAk9Vvza6SUCPczESstGcMArQufwjkuJmWH90',
    );
  }

  /**
   * Get some invalid field values.
   * @return array of fields
   */
  function getInvalidAccount() {
    return array(
      'api_key' => 'hello',
    );
  }

  function doTestValidValues($balances) {
    $this->assertEquals(0, $balances['btc']['confirmed']);
  }

  function doTestValidSecurities($securities) {
    // should be empty
    $this->assertEquals(0, count($securities), "Expected 0 securities");
  }

}
