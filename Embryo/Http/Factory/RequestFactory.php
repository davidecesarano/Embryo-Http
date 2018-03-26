<?php
    
    /**
     * RequestFactory
     * 
     * PSR-17 factory for creating a new HTTP request.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http
     * @see    https://github.com/http-interop/http-factory/blob/master/src/RequestFactoryInterface.php
     */

    namespace Embryo\Http\Factory;
    
    use Embryo\Http\Message\Request;
    use Embryo\Http\Factory\UriFactory;
    use Interop\Http\Factory\RequestFactoryInterface;
    use Psr\Http\Message\RequestInterface;
    
    class RequestFactory implements RequestFactoryInterface
    {
        /**
         * Creates a HTTP Request.
         *
         * @param string $method
         * @param UriInterface|string $uri
         * @return RequestInterface
         */
        public function createRequest($method, $uri): RequestInterface
        {
            $uri = (is_string($uri)) ? (new UriFactory)->createUri($uri) : $uri;
            return new Request($method, $uri);
        }
    }