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
    use Embryo\Http\Factory\{UploadedFileFactory, UriFactory};
    use Psr\Http\Message\{ServerRequestFactoryInterface, ServerRequestInterface};

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
            $uri = (is_string($uri)) ? (new UriFactory)->createUri($uri) : $uri;
            return new ServerRequest($method, $uri, $serverParams);
        }

        /**
         * Creates a new server-side request from server.
         *
         * @param array $server
         * @param array $get
         * @param array $post
         * @param array $cookie
         * @param array $files
         * @return ServerRequestInterface
         */
        public function createServerRequestFromServer(
            array $server, 
            array $get, 
            array $post, 
            array $cookie, 
            array $files
        ): ServerRequestInterface
        {
            $method  = $server['REQUEST_METHOD'];
            $uri     = (new UriFactory)->createUriFromServer($server);
            $query   = $get;
            $post    = $post;
            $cookies = $cookie; 
            $files   = (new UploadedFileFactory)->createUploadedFileFromServer($files);
            
            $request = new ServerRequest($method, $uri, $server);
            $request = $request->withCookieParams($cookies);
            $request = $request->withQueryParams($query);
            $request = $request->withUploadedFiles($files);
            $request = $request->withParsedBody($post);
            return $request;
        }
    }