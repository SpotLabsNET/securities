<?php

namespace SecurityExchange;

use \Openclerk\Currencies\SimpleSecurityExchange;
use \Openclerk\Currencies\SecurityExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

/**
 * Represents the Havelock Investments security exchange.
 */
class HavelockInvestments extends SimpleSecurityExchange {

  function getName() {
    return "Havelock Investments";
  }

  function getCode() {
    return "havelock";
  }

  function getURL() {
    return "https://www.havelockinvestments.com/";
  }

  function fetchAllRates(Logger $logger) {

    $url = "https://www.havelockinvestments.com/r/tickerfull";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    $result = array();
    foreach ($json as $security) {
      // skip any zero-traded symbols
      if ($security['last'] == 0) {
        continue;
      }

      $result[$security['symbol']] = array(
        // all havelock codes are in btc
        'currency' => 'btc',
        'security' => $security['symbol'],
        'name' => $security['name'],
        'last_trade' => $security['last'],
        'units' => $security['units'],
        'volume' => $security['1d']['vol'],
      );
    }

    return $result;

  }

}
