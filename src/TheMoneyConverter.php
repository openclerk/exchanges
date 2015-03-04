<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class TheMoneyConverter extends SimpleExchange {

  function getName() {
    return "TheMoneyConverter";
  }

  function getCode() {
    return "themoneyconverter";
  }

  function getURL() {
    return "http://themoneyconverter.com/";
  }

  /**
   * Convert the given Poloniex currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   * so that it can be used in openclerk/currencies
   */
  function getCurrencyCode($str) {
    switch ($str) {
      case "ghs": return "ghana";

      default:
        return strtolower($str);
    }
  }

  function fetchAllRates(Logger $logger) {
    $url = "http://themoneyconverter.com/rss-feed/USD/rss.xml";
    $logger->info($url);

    $raw = Fetch::get($url);

    $result = array();

    // load as XML
    $xml = new \SimpleXMLElement($raw);
    $nodes = $xml->xpath("/rss/channel/item");
    foreach ($nodes as $node) {
      $title = (string) $node->title;
      $description = (string) $node->description;

      $pair = explode("/", $title, 2);
      $currency1 = $this->getCurrencyCode($pair[1]);
      $currency2 = $this->getCurrencyCode($pair[0]);

      if ($currency1 == $currency2) {
        // no need to insert the value '1.0'
        continue;
      }

      if (preg_match("#1 [^=]+ = ([0-9\.]+) #i", $description, $matches)) {
        if ($matches[1] == 0) {
          // prevent div/0
          continue;
        }

        $result[] = array(
          'currency1' => $currency1,
          'currency2' => $currency2,
          'last_trade' => 1.0 / $matches[1],      // flip the values around; issue #423
        );
      }
    }

    return $result;

  }

}
