<?php 

    /**
     * ServerRequestFactory
     * 
     * PSR-17 factory for creating new server-side HTTP request.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http  
     * @see    https://github.com/http-interop/http-factory/blob/master/src/ServerRequestFactoryInterface.php
     */

    namespace Embryo\Http\Factory;

    use Embryo\Http\Message\ServerRequest;
    use Embryo\Http\Factory\{StreamFactory, UploadedFileFactory, UriFactory};
    use Psr\Http\Message\{ServerRequestFactoryInterface, ServerRequestInterface, UriInterface};

    class ServerRequestFactory implements ServerRequestFactoryInterface
    {
        /**
         * Creates a new server-side request.
         *
         * @param string $method
         * @param UriInterface|string $uri
         * @param array $serverParams
         * @return ServerRequestInterface
         */
        public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
        {
            return new ServerRequest($method, $uri, $serverParams);
        }

        /**
         * Creates a new server-side request from server.
         *
         * @return ServerRequestInterface
         */
        public function createServerRequestFromServer(): ServerRequestInterface
        {
            $method  = $_SERVER['REQUEST_METHOD'];
            $uri     = (new UriFactory)->createUriFromServer($_SERVER);
            $files   = (new UploadedFileFactory)->createUploadedFileFromServer($_FILES);
            
            $request = $this->createServerRequest($method, $uri, $_SERVER);
            $request = $request->withQueryParams($_GET);
            $request = $request->withParsedBody($_POST);
            $request = $request->withCookieParams($_COOKIE);
            $request = $request->withUploadedFiles($files);

            $contentType = $request->getHeaderLine('Content-Type');
            if ($contentType !== '' && strpos($contentType, 'application/json') !== false) {

                $streamWrite = fopen('php://temp', 'w+');
                $streamRead = fopen('php://input', 'r');
                if ($streamWrite && $streamRead) {
                    stream_copy_to_stream($streamRead, $streamWrite);
                    rewind($streamWrite);
                    $body    = (new StreamFactory)->createStreamFromResource($streamWrite);
                    $params  = json_decode($body->getContents(), true);
                    $request = $request->withBody($body)->withParsedBody($params);
                }
                return $request;

            }
            return $request;
        }
    }