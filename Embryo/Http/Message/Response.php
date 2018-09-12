<?php 
    
    /**
     * Response
     * 
     * PSR-7 implementation of an outgoing, server-side response.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http 
     * @see    https://github.com/php-fig/http-message/blob/master/src/ResponseInterface.php
     */

    namespace Embryo\Http\Message;
    
    use Embryo\Http\Factory\StreamFactory;
    use Embryo\Http\Message\{Headers, Message};
    use Embryo\Http\Message\Traits\ResponseTrait;
    use Psr\Http\Message\{ResponseInterface, StreamInterface};
    
    class Response extends Message implements ResponseInterface 
    {
        use ResponseTrait;
        
        /**
         * @var int $status
         */
        protected $status = 200;
        
        /**
         * @var string $reasonPhrase
         */
        protected $reasonPhrase = '';
        
        /**
         * Creates new HTTP response.
         * 
         * @param int $code 
         * @param string $reasonPhrase
         * @param array $headers 
         * @param StreamInterface $body
         */
        public function __construct(int $status = 200, string $reasonPhrase = '', array $headers = [], StreamInterface $body = null)
        {
            $this->status       = $this->filterStatus($status);
            $this->headers      = $this->setHeaders($headers);
            $this->body         = $body ? $body : (new StreamFactory)->createStream('');
            $this->reasonPhrase = $this->filterReasonPhrase($status, $reasonPhrase);
        }
        
        /**
         * Gets the response status code.
         *
         * @return int 
         */
        public function getStatusCode()
        {
            return $this->status;
        }
        
        /**
         * Returns an instance with the specified status code and, optionally, reason phrase.
         *
         * @param int $status 
         * @param string $reasonPhrase
         * @return static
         * @throws InvalidArgumentException
         */
        public function withStatus($status, $reasonPhrase = '')
        {
            $status = $this->filterStatus($status);
            $reasonPhrase = $this->filterReasonPhrase($status, $reasonPhrase);

            $clone = clone $this;
            $clone->status = $status;
            $clone->reasonPhrase = $reasonPhrase;
            return $clone;
        }
        
        /**
         * Gets the response reason phrase associated with the status code.
         *
         * @return string
         */
        public function getReasonPhrase()
        {   
            if ($this->reasonPhrase !== '') {
                return $this->reasonPhrase;
            } else {
                
                if (isset($this->messages[$this->status])) {
                    return $this->messages[$this->status];
                }

            }
            return '';
        }
    }