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
    use Psr\Http\Message\{ResponseFactoryInterface, ResponseInterface};

    class ResponseFactory implements ResponseFactoryInterface
    {
        /**
         * Creates new response.
         *
         * @param int $code
         * @param string $reasonPhrase
         * @return ResponseInterface
         */
        public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
        {
            return new Response($code, $reasonPhrase);
        }
    }