# termii-api-client-php

[![Latest Stable Version](https://img.shields.io/github/v/release/brokeyourbike/termii-api-client-php)](https://github.com/brokeyourbike/termii-api-client-php/releases)
[![Total Downloads](https://poser.pugx.org/brokeyourbike/termii-api-client/downloads)](https://packagist.org/packages/brokeyourbike/termii-api-client)
[![License: MPL-2.0](https://img.shields.io/badge/license-MPL--2.0-purple.svg)](https://github.com/brokeyourbike/termii-api-client-php/blob/main/LICENSE)

[![Maintainability](https://api.codeclimate.com/v1/badges/1cd42fecafb04e6ed6ff/maintainability)](https://codeclimate.com/github/brokeyourbike/termii-api-client-php/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/1cd42fecafb04e6ed6ff/test_coverage)](https://codeclimate.com/github/brokeyourbike/termii-api-client-php/test_coverage)
[![tests](https://github.com/brokeyourbike/termii-api-client-php/actions/workflows/tests.yml/badge.svg)](https://github.com/brokeyourbike/termii-api-client-php/actions/workflows/tests.yml)

Termii API Client for PHP

## Installation

```bash
composer require brokeyourbike/termii-api-client
```

## Usage

```php
use BrokeYourBike\Termii\Client;

$apiClient = new Client($config, $httpClient);
$apiClient->fetchBalanceRaw();
```

## License
[Mozilla Public License v2.0](https://github.com/brokeyourbike/termii-api-client-php/blob/main/LICENSE)
