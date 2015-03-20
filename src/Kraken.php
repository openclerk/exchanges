<?php

namespace Exchange;

use \Openclerk\Currencies\SimpleExchange;
use \Openclerk\Currencies\ExchangeRateException;
use \Monolog\Logger;
use \Apis\Fetch;

class Kraken extends SimpleExchange {

  function getName() {
    return "Kraken";
  }

  function getCode() {
    return "kraken";
  }

  function getURL() {
    return "https://www.kraken.com/";
  }

  static $currency_code_map = array(
    "XXBT" => "btc",
    "XLTC" => "ltc",
    "XXRP" => "xrp",
    "XNMC" => "nmc",
    "XXDG" => "dgc",
    "XSTR" => "str",
    "XXVN" => "ven",

    "ZUSD" => "usd",
    "ZEUR" => "eur",
    "ZGBP" => "gbp",
    "ZKRW" => "krw",
    "ZCAD" => "cad",
    "ZCNY" => "cny",
    "ZRUB" => "rur",
    "ZJPY" => "jpy",
    "ZAUD" => "aud",
  );

  /**
   * Convert the given Kraken currency code (uppercase)
   * into the openclerk/currencies currency code (lowercase, three characters)
   */
  function getCurrencyCode($str) {
    if (isset(self::$currency_code_map[$str])) {
      return self::$currency_code_map[$str];
    }

    return strtolower($str);
  }

  function getKrakenCode($str) {
    $index = array_search($str, self::$currency_code_map);
    if ($index !== false) {
      return $index;
    }

    return strtoupper($str);
  }

  /**
   * There seems to be an arbitrary ordering of Kraken exchange pairs;
   * here we switch them to openclerk/exchanges order
   * @param $currency1 3-character code
   * @param $currency2 3-character code
   */
  function needsSwitch($currency1, $currency2) {
    switch ($currency1 . $currency2) {
      case "ltceur":
      case "ltcusd":
      case "btceur":
      case "btcusd":
      case "btcjpy":
      case "btcgbp":
        return true;

      default:
        return false;
    }
  }

  public function fetchMarkets(Logger $logger) {
    $url = "https://api.kraken.com/0/public/AssetPairs";
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));

    $result = array();
    foreach ($json['result'] as $pair => $info) {
      $currency1 = $this->getCurrencyCode(substr($pair, 0, 4));
      $currency2 = $this->getCurrencyCode(substr($pair, 4, 4));

      if ($this->needsSwitch($currency1, $currency2)) {
        $temp = $currency2;
        $currency2 = $currency1;
        $currency1 = $temp;
      }

      $result[] = array($currency1, $currency2);
    }

    return $result;
  }

  function getKrakenMarket($currency1, $currency2) {
    if ($this->needsSwitch($currency2, $currency1)) {
      return $this->getKrakenCode($currency2) . $this->getKrakenCode($currency1);
    } else {
      return $this->getKrakenCode($currency1) . $this->getKrakenCode($currency2);
    }
  }

  function fetchAllRates(Logger $logger) {
    $result = array();
    $keys = array();
    foreach ($this->fetchMarkets($logger) as $pair) {
      $keys[] = $this->getKrakenMarket($pair[0], $pair[1]);
    }

    $url = "https://api.kraken.com/0/public/Ticker?pair=" . implode(",", $keys);
    $logger->info($url);

    $json = Fetch::jsonDecode(Fetch::get($url));
    if ($json['error']) {
      throw new ExchangeRateException(implode(", ", $json['error']));
    }

    foreach ($this->fetchMarkets($logger) as $pair) {
      $currency1 = $pair[0];
      $currency2 = $pair[1];

      $key = $this->getKrakenMarket($pair[0], $pair[1]);

      $rate = $json['result'][$key];
      if (!isset($rate['c'][0])) {
        throw new ExchangeRateException("Could not find any last rate for $key");
      }

      // thanks for obsfucating, Kraken!
      // p = volume weighted average price array(<today>, <last 24 hours>),
      // t = number of trades array(<today>, <last 24 hours>),
      // l = low array(<today>, <last 24 hours>),
      // h = high array(<today>, <last 24 hours>),
      // o = today's opening price

      $rate = array(
        "currency1" => $currency1,
        "currency2" => $currency2,
        "last_trade" => $rate['c'][0],
        "bid" => $rate['b'][0],
        "ask" => $rate['a'][0],
        "volume" => $rate['v'][1],
        "high" => $rate['h'][0],
        "low" => $rate['l'][0],
      );

      // issue #456: swap over Kraken ticker data
      if ($rate['currency1'] == 'btc') {
        $copy = $rate;
        $rate['last_trade'] = 1 / $copy['last_trade'];
        $rate['bid'] = 1 / $copy['ask'];
        $rate['ask'] = 1 / $copy['bid'];
        $rate['volume'] = $copy['volume'] / $copy['last_trade'];
        $rate['high'] = 1 / $copy['low'];
        $rate['low'] = 1 / $copy['high'];
      }

      $result[] = $rate;
    }

    return $result;
  }

}
