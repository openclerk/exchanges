openclerk/exchanges [![Build Status](https://travis-ci.org/openclerk/exchanges.svg?branch=master)](https://travis-ci.org/openclerk/exchanges)
===================

A library for accessing live exchange market data for many different exchanges,
used by [Openclerk](http://openclerk.org) and live on [CryptFolio](https://cryptfolio.com).

This extends on the abstract currency definitions provided by
[openclerk/currencies](https://github.com/openclerk/currencies).

## Installing

Include `openclerk/exchanges` as a requirement in your project `composer.json`,
and run `composer update` to install it into your project:

```json
{
  "require": {
    "openclerk/exchanges": "dev-master"
  }
}
```

* [Exchanges supported](https://github.com/openclerk/exchanges/tree/master/src)

## Using

Get the markets supported by an exchanges:

```php
use \Monolog\Logger;

$logger = new Logger("log");

$exchange = new \Exchange\BTCe();
print_r($exchange->fetchMarkets($logger));
```

Get the current trade values for an exchange market, which will always include
at least `last_trade`, and may include `bid`, `ask`, `high`, `low`, `volume`, `avg`
and/or `vwap`:

```php
$exchange = new \Exchange\BitNZ();
print_r($exchange->fetchRates('nzd', 'btc', $logger));
```

## Tests

Each exchange comes with a suite of tests to check each associated service.

```
composer install
vendor/bin/phpunit
```

To run the tests for a single exchange:

```
vendor/bin/phpunit --bootstrap "vendor/autoload.php" test/CoinbaseTest
```

To get debug output for the tests (such as CURL requests and decoded output),
add the `--debug` switch to your `vendor/bin/phpunit` command.

## Donate

[Donations are appreciated](https://code.google.com/p/openclerk/wiki/Donating).

## Contributing

Pull requests that contribute new exchanges are welcome.

For new currencies, make sure that you also provide an associated
`CurrencyTest` so that the currency is automatically testable.

## TODO

1. Generate README list of currencies/services automatically
1. Link to live APIs on CryptFolio
1. CI build server and link to test results
