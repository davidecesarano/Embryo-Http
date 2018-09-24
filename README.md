# Embryo Http
A PSR-7 and PSR-17 implementation for HTTP messages and factory. 
An HTTP message is either a request from a client to a server or a response from a server to a client. An HTTP factory is a method by which a new HTTP object, as defined by PSR-7, is created. 

## Requirements
* PHP >= 7.1

## Installation
Using Composer:
```
$ composer require davidecesarano/embryo-http
```

## Usage
### Request
```php
$request = (new RequestFactory)->createRequest('GET', 'http://example.com');
```
### Server Request
```php
// create server request
$request = (new RequestFactory)->createServerRequest('GET', 'http://example.com');

// create server request from server
$request = (new RequestFactory)->createServerRequestFromServer();
```
### Response
```php
$request = (new ResponseFactory)->createResponse(200);
```

### 
