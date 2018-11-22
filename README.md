# Embryo-Emitter
Emitter for PSR-7 response.

## Requirements
* PHP >= 7.1
* A [PSR-7](https://www.php-fig.org/psr/psr-7/) http message implementation and [PSR-17](https://www.php-fig.org/psr/psr-17/) http factory implementation (ex. [Embryo-Http](https://github.com/davidecesarano/Embryo-Http))

## Installation
Using Composer:
```
$ composer require davidecesarano/embryo-emitter
```

## Usage
Emitting a PSR-7 response content via `echo`.
```php
use Embryo\Http\Factory\ResponseFactory;
use Embryo\Http\Emitter\Emitter;

$response = (new ResponseFactory)->createResponse(200);
$emitter  = new Emitter;

$body = $response->getBody();
$body->write('Hello World!');
$response = $response->withBody($body);

$emitter->emit($response); // Hello World!
```
