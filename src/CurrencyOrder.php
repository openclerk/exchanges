<?php

namespace Exchange;

/**
 * In openclerk/exchanges, we want to return all exchange pairs according to a
 * particular currency order. For example, we want all exchanges to return
 * "USD/BTC" (USD for one BTC), rather than "BTC/USD" - this keeps business
 * logic consistent in applications that use this component.
 *
 * This class defines the intended order, and some helper methods for asserting
 * that this order is used.
 *
 * Currencies that are not defined in this class can appear in any order.
 */
class CurrencyOrder {

  static $order = array(
    // fiat currencies
    'usd',
    'eur',
    'cad',
    'aud',
    'nzd',
    // TODO list more

    // cryptocurrencies
    'btc',
    'ltc',
    'nmc',
    'dog',
    'xrp',
    'xpm',
    'ppc',
    'dvc',
    'trc',
    'qrk',
    'dgc',
    'wdc',
    'bc1',
    'ftc',
    // TODO list more

    // commodities
    'ghs',
  );

  static function hasOrder($c) {
    return in_array($c, self::$order);
  }

  static function isOrdered($c1, $c2) {
    return array_search($c1, self::$order) <= array_search($c2, self::$order);
  }

}
