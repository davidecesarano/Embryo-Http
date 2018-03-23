<?php
    
    /**
     * ResponseFactory
     * 
     * PSR-17 factory for creating a new response.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http
     * @see    https://github.com/http-interop/http-factory/blob/master/src/ResponseFactoryInterface.php
     */
    
    namespace Embryo\Http\Factory;
    
    use Embryo\Http\Message\Response;
    use Interop\Http\Factory\ResponseFactoryInterface;
    use Psr\Http\Message\ResponseInterface;

    class ResponseFactory implements ResponseFactoryInterface
    {
        /**
         * Creates new response.
         *
         * @param int $code
         * @return ResponseInterface
         */
        public function createResponse($code = 200): ResponseInterface
        {
            return new Response($code);
        }
    }