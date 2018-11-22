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

## Factory

### RequestFactory 
```php
$request = (new RequestFactory)->createRequest('GET', 'http://example.com');
```

### ResponseFactory 
```php
$response = (new ResponseFactory)->createResponse(200);
```

### ServerRequestFactory  
```php
// create a new server-side request
$request = (new ServerRequestFactory)->createServerRequest('GET', 'http://example.com');

// create a new server-side request from server
$request = (new ServerRequestFactory)->createServerRequestFromServer();
```

### StreamFactory  
```php
// create a new stream from a string
$stream = (new StreamFactory)->createStream('Hello World!');

// create a stream from an existing file
$stream = (new StreamFactory)->createStreamFromFile('/path/file');

// create a new stream from an existing resource
$resource = fopen('php://temp', 'w+');
$stream = (new StreamFactory)->createStreamFromResource($resource);
```

### UploadedFileFactory   
```php
// create a new uploaded file
$file = (new StreamFactory)->createStreamFromFile('/path/file');
$upload = (new UploadedFileFactory)->createUploadedFile($file);

// create a new uploaded file from server
$upload = (new UploadedFileFactory)->createUploadedFileFromServer($_FILES);
```

### UriFactory
```php
// create new uri from string
$uri = (new UriFactory)->createUri('http://example.com');

// create new uri from server
$uri = (new UriFactory)->createUriFromServer($_SERVER);
```