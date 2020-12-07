<?php 

    /**
     * BodyTrait
     * 
     * This trait supports Request and Response class for
     * set the Body.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http 
     */

    namespace Embryo\Http\Message\Traits;

    use Psr\Http\Message\StreamInterface;
    use Embryo\Http\Factory\StreamFactory;

    trait BodyTrait 
    {
        /**
         * Set body. 
         * 
         * @param StreamInterface|string|null $body
         * @return StreamInterface
         */
        protected function setBody($body = null): StreamInterface
        {
            if ($body instanceof StreamInterface) {
                return $body;
            } else if (is_string($body)) {
                return (new StreamFactory)->createStream($body);
            } else {
                return (new StreamFactory)->createStream('');
            }
        }
    }