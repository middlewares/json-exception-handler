# middlewares/json-exception-handler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]

Catches exceptions that occur during request handling and output them as JSON.

## Requirements

* PHP >= 7.2
* A [PSR-7](https://packagist.org/providers/psr/http-message-implementation) http message implementation ([Diactoros](https://github.com/zendframework/zend-diactoros), [Guzzle](https://github.com/guzzle/psr7), [Slim](https://github.com/slimphp/Slim), etc...)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/json-exception-handler](https://packagist.org/packages/middlewares/json-exception-handler).

```sh
composer require middlewares/json-exception-handler
```

## JsonExceptionHandler

Catches exceptions thrown that occur later in request processing an creates a new response with HTTP 500 status and JSON encoded version of the exception as the body.

### `contentType(string $type)`

Change the Content-Type header of the response. Default is `application/json`.

### `includeTrace(bool $enable)`

Enable or disable the stack trace in the response. Default is `true`.

### `jsonOptions(int $options)`

Set options for `json_encode` of the exception. Default is `0`.

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/json-exception-handler.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ga]: https://github.com/middlewares/json-exception-handler/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/json-exception-handler.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/json-exception-handler
[link-downloads]: https://packagist.org/packages/middlewares/json-exception-handler
