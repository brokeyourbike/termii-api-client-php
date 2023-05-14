# termii-api-client

[![Latest Stable Version](https://img.shields.io/github/v/release/brokeyourbike/termii-api-client-php)](https://github.com/brokeyourbike/termii-api-client-php/releases)
[![Total Downloads](https://poser.pugx.org/brokeyourbike/termii-api-client/downloads)](https://packagist.org/packages/brokeyourbike/termii-api-client)
[![Maintainability](https://api.codeclimate.com/v1/badges/1cd42fecafb04e6ed6ff/maintainability)](https://codeclimate.com/github/brokeyourbike/termii-api-client-php/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/1cd42fecafb04e6ed6ff/test_coverage)](https://codeclimate.com/github/brokeyourbike/termii-api-client-php/test_coverage)

Termii API Client for PHP

## Installation

```bash
composer require brokeyourbike/termii-api-client
```

## Usage

```php
use BrokeYourBike\Termii\Client;
use BrokeYourBike\Termii\Interface\ApiConfigInterface;

assert($config instanceof ApiConfigInterface);
assert($httpClient instanceof \GuzzleHttp\ClientInterface);

$apiClient = new Client($config, $httpClient);
$apiClient->fetchBalanceRaw();
```

## Authors
- [Ivan Stasiuk](https://github.com/brokeyourbike) | [Twitter](https://twitter.com/brokeyourbike) | [LinkedIn](https://www.linkedin.com/in/brokeyourbike) | [stasi.uk](https://stasi.uk)

## License
[Mozilla Public License v2.0](https://github.com/brokeyourbike/termii-api-client-php/blob/main/LICENSE)
