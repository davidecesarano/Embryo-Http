<?php 
    
    /**
     * Request
     * 
     * PSR-7 implementation of an outgoing, client-side request.
     *
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http
     * @see    https://github.com/php-fig/http-message/blob/master/src/RequestInterface.php
     */

    namespace Embryo\Http\Message;
    
    use Embryo\Http\Message\Message;
    use Embryo\Http\Message\Traits\{RequestTrait, BodyTrait};
    use Psr\Http\Message\{RequestInterface, StreamInterface, UriInterface};
    
    class Request extends Message implements RequestInterface 
    {
        use BodyTrait;
        use RequestTrait;

        /**
         * @var string $method
         */
        protected $method;
        
        /**
         * @var UriInterface $uri
         */
        protected $uri;
        
        /**
         * @var string $requestTarget
         */
        protected $requestTarget;

        /**
         * Creates HTTP Request.
         *
         * This method update the Host header if the URI contains 
         * a host component or Host header is not available.
         * 
         * @param string $method
         * @param string|UriInterface $uri
         * @param array $headers
         * @param StreamInterface|string|null $body
         */
        public function __construct(string $method, $uri, array $headers = [], $body = null)
        {
            $this->method = $this->filterMethod($method);
            $this->uri = $this->setUri($uri);
            $this->headers = $this->setHeaders($headers);                
            $this->body = $this->setBody($body);
            $this->requestTarget = $this->setRequestTarget($this->uri->getPath(), $this->uri->getQuery());
            $this->headers['host'] = $this->setNotPreserveHost($this->uri->getHost());
        }
        
        /**
         * ------------------------------------------------------------
         * REQUEST TARGET
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves the message's request target.
         * 
         * If no URI is available, and no request-target has been specifically 
         * provided, this method return the string "/".
         * 
         * @return string
         */
        public function getRequestTarget()
        {
            return $this->requestTarget;
        }

        /**
         * Returns an instance with the specific request-target.
         * 
         * @param mixed $requestTarget
         * @return static
         */
        public function withRequestTarget($requestTarget)
        {
            $clone = clone $this;
            $clone->requestTarget = $requestTarget;
            return $clone;
        }
        
        /**
         * ------------------------------------------------------------
         * METHOD
         * ------------------------------------------------------------
         */
        
        /**
         * Returns an instance with the provided HTTP method.
         *
         * @return string
         */
        public function getMethod()
        {
            return $this->method;
        }
        
        /**
         * Returns an instance with the provided HTTP method.
         *
         * @param string $method 
         * @return static 
         */
        public function withMethod($method)
        {
            $clone = clone $this;
            $clone->method = $this->filterMethod($method);
            return $clone;
        }
        
        /**
         * ------------------------------------------------------------
         * URI
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves the URI instance.
         *
         * @return UriInterface
         */
        public function getUri()
        {
            return $this->uri;
        }
        
        /**
         * Returns an instance with the provided URI.
         *
         * This method update the Host header of the returned request by
         * default if the URI contains a host component.
         * 
         * @param UriInterface $uri 
         * @param bool $preserveHost
         * @return static
         */
        public function withUri(UriInterface $uri, $preserveHost = false)
        {
            $clone = clone $this;
            $clone->uri = $uri;
            $host = $uri->getHost();

            if (!$preserveHost) {
                $clone->headers['host'] = $this->setNotPreserveHost($host);
            } else {
                $headerHost = $this->getHeaderLine('Host');
                $clone->headers['host'] = $this->setPreserveHost($headerHost, $host);
            }
            return $clone;
        }
    }