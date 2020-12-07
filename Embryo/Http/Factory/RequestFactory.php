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
    use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, UriInterface};
    
    class RequestFactory implements RequestFactoryInterface
    {
        /**
         * Creates a HTTP Request.
         *
         * @param string $method
         * @param UriInterface|string $uri
         * @return RequestInterface
         */
        public function createRequest(string $method, $uri): RequestInterface
        {
            return new Request($method, $uri);
        }
    }