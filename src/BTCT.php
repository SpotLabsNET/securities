<?php

namespace SecurityExchange;

use \Openclerk\Currencies\DisabledExchange;
use \Openclerk\Currencies\SimpleSecurityExchange;
use \Openclerk\Currencies\SecurityExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

/**
 * Represents the BTC Trading Co. security exchange.
 */
class BTCT extends SimpleSecurityExchange implements DisabledExchange {

  function disabledAt() {
    return "2014-03-21";
  }

  function getName() {
    return "BTC Trading Co.";
  }

  function getCode() {
    return "btct";
  }

  function getURL() {
    return "https://btct.co/";
  }

  function fetchAllRates(Logger $logger) {
    throw new SecurityExchangeRateException("Cannot fetch rates for a disabled security exchange");
  }

}
