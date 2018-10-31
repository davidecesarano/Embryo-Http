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
* [Request](#request)
* [ServerRequest](#serverrequest)
* [Response](#response)
* [Stream](#stream)
* [Uri](#uri)
* [UploadedFile](#uploadedfile)
### Request
```php
$request = (new RequestFactory)->createRequest('GET', 'http://example.com');
```

### ServerRequest

#### Create Server Request
```php
$request = (new RequestFactory)->createServerRequest('GET', 'http://example.com');
```

#### Create Server Request from Server
```php
$request = (new RequestFactory)->createServerRequestFromServer();
```

### Response
```php
$response = (new ResponseFactory)->createResponse(200);
```

### Stream

#### Create Stream from a string
```php
$stream = (new StreamFactory)->createStream('Hello World!');
echo $stream; // Hello World!
```

#### Create Stream from a file
```php
$stream = (new StreamFactory)->createStreamFromFile('/path/file', 'w+');
$stream->write('Hello World!');
echo $stream; // Hello World!
```

#### Create Stream from a resource
```php
$resource = fopen('php://temp', 'w+');
$stream = (new StreamFactory)->createStreamFromResource($resource);
$stream->write('Hello World!');
echo $stream; // Hello World!
```

### Uri

### UploadedFile
