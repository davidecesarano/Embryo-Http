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

    use InvalidArgumentException;
    use Embryo\Http\Message\ServerRequest;
    use Embryo\Http\Factory\{UploadedFileFactory, UriFactory};
    use Psr\Http\Message\ServerRequestInterface;
    use Interop\Http\Factory\ServerRequestFactoryInterface;

    class ServerRequestFactory implements ServerRequestFactoryInterface
    {
        /**
         * Creates a new server-side request.
         *
         * @param string $method
         * @param UriInterface|string $uri
         * @return ServerRequestInterface
         */
        public function createServerRequest($method, $uri): ServerRequestInterface
        {
            $uri = (is_string($uri)) ? (new UriFactory)->createUri($uri) : $uri;
            return new ServerRequest($method, $uri);
        }

        /**
         * Creates a new server-side request from gloablas variables.
         *
         * @param array $globals
         * @return ServerRequestInterface
         * @throws InvalidArgumentException
         */
        public function createServerRequestFromArray(array $globals): ServerRequestInterface
        {
            $method  = $globals['server']['REQUEST_METHOD'];
            if (!is_string($method)) {
                throw new InvalidArgumentException('Request method must be a string');
            }

            $uri     = (new UriFactory)->createUriFromArray($globals['server']);
            $server  = $globals['server'];
            $cookies = $globals['cookie']; 
            $query   = $globals['query'];
            $files   = (new UploadedFileFactory)->createUploadedFileFromArray($globals['files']);
            $post    = $globals['post'];

            $request = new ServerRequest($method, $uri, $server);
            $request = $request->withCookieParams($cookies);
            $request = $request->withQueryParams($query);
            $request = $request->withUploadedFiles($files);
            $request = $request->withParsedBody($post);
            return $request;
        }
    }