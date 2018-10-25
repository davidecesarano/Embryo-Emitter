# Embryo-Emitter
Emitter for PSR-7 response.

## Requirements
* PHP >= 7.1
* A [PSR-7](https://www.php-fig.org/psr/psr-7/) http message implementation (ex. [Embryo-Http](https://github.com/davidecesarano/embryo-http))

## Installation
```
$ composer require davidecesarano/embryo-emitter
```

## Usage
Emitting a PSR-7 response content via `echo`.
```php
use Embryo\Http\Factory\ResponseFactory;
use Embryo\Http\Emitter\Emitter;

$response = (new ResponseFactory)->createResponse(200);
$response->write('Hello World!');
$emitter = new Emitter;
$emitter->emit($response); // Hello World!
```
