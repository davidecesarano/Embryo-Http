<?php 
    
    /**
     * Message
     * 
     * PSR-7 HTTP messages consist of requests from a client to a server and responses
     * from a server to a client.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http
     * @see    https://github.com/php-fig/http-message/blob/master/src/MessageInterface.php
     */
    
    namespace Embryo\Http\Message;

    use Embryo\Http\Message\Traits\HeadersTrait;
    use Psr\Http\Message\{MessageInterface, StreamInterface};

    class Message implements MessageInterface 
    {
        use HeadersTrait;

        /**
         * @var string $protocolVersion
         */
        protected $protocolVersion = '1.1';
        
        /**
         * @var array $headers
         */
        protected $headers;
        
        /**
         * @var StreamInterface $body
         */
        protected $body;

        /**
         * ------------------------------------------------------------
         * PROTOCOL
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves the HTTP protocol version as a string.
         *
         * @return string 
         */
        public function getProtocolVersion()
        {
            return $this->protocolVersion;
        }
        
        /**
         * Returns an instance with the specified HTTP protocol version.
         *
         * @param string $version
         * @return static 
         */
        public function withProtocolVersion($version)
        {
            $clone = clone $this;
            $clone->protocolVersion = $version;
            return $clone;
        }
        
        /**
         * ------------------------------------------------------------
         * HEADERS
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves all message header values.
         * 
         * The keys represent the header name as it will be sent over the wire, and
         * each value is an array of strings associated with the header. While header 
         * names are not case-sensitive, getHeaders() will preserve the exact case in 
         * which headers were originally specified.
         *
         * @return string[][]
         */
        public function getHeaders()
        {
            $headers = [];
            if (!empty($this->headers)) {
                foreach ($this->headers as $key => $value) {
                    $headers[$value['original']] = $value['values'];
                }
            }
            return $headers;
        }
        
        /**
         * Checks if a header exists by the given case-insensitive name.
         *
         * @param string $name
         * @return bool 
         */
        public function hasHeader($name)
        {
            $name = $this->setHeaderName($name);
            return array_key_exists($name, $this->headers);
        }
        
        /**
         * Retrieves a message header value by the given case-insensitive name.
         * 
         * This method returns an array of all the header values of the given
         * case-insensitive header name. If the header does not appear in the message, 
         * this method return an empty array.
         *
         * @param string $name 
         * @return string[] 
         */
        public function getHeader($name)
        {
            $name = $this->setHeaderName($name);
            return ($this->hasHeader($name)) ? $this->headers[$name]['values'] : [];
        }
        
        /**
         * Retrieves a comma-separated string of the values for a single header.
         * 
         * This method returns all of the header values of the given
         * case-insensitive header name as a string concatenated together using
         * a comma. If the header does not appear in the message, this method 
         * return an empty string.
         *
         * @param string $name 
         * @return string 
         */
        public function getHeaderLine($name)
        {
            return ($this->hasHeader($name)) ? implode(',', $this->getHeader($name)) : '';
        }
        
        /**
         * Returns an instance with the provided value replacing the specified header.
         * 
         * While header names are case-insensitive, the casing of the header will
         * be preserved by this function, and returned from getHeaders().
         * 
         * @param string $name
         * @param string|string[] $value
         * @return static
         * @throws \InvalidArgumentException
         */
        public function withHeader($name, $value)
        {
            if (!is_string($name)) {
                throw new \InvalidArgumentException('Header name must be a string');
            }

            if (!is_string($value) && !is_array($value)) {
                throw new \InvalidArgumentException('Header value must be a string or array');
            }

            $original = $name;
            $name = $this->setHeaderName($name);
            $value = [
                'original' => $original, 
                'values'   => is_array($value) ? $value : [$value]
            ];

            $clone = clone $this;
            $clone->headers[$name] = $value;
            return $clone;
        }
        
        /**
         * Returns an instance with the specified header appended with the given value.
         * 
         * Existing values for the specified header will be maintained. The new
         * value(s) will be appended to the existing list. If the header did not
         * exist previously, it will be added. 
         *
         * @param string $name 
         * @param string|string[] $value 
         * @return static 
         * @throws \InvalidArgumentException
         */
        public function withAddedHeader($name, $value)
        {
            if (!is_string($name)) {
                throw new \InvalidArgumentException('Header name must be a string');
            }

            if (!is_string($value) && !is_array($value)) {
                throw new \InvalidArgumentException('Header value must be a string or array');
            }

            $original = $name;
            $name = $this->setHeaderName($name);
            $oldValues = $this->getHeader($name);
            $newValues = is_array($value) ? $value : [$value];
            $value = [
                'original' => $original, 
                'values'   => array_merge($oldValues, array_values($newValues))
            ];

            $clone = clone $this;
            $clone->headers[$name] = $value;
            return $clone;
        }
        
        /**
         * Returns an instance without the specified header.
         *
         * @param string $name 
         * @return static 
         */
        public function withoutHeader($name)
        {
            $name = $this->setHeaderName($name);
            $clone = clone $this;
            unset($clone->headers[$name]);
            return $clone;    
        }
        
        /**
         * ------------------------------------------------------------
         * BODY
         * ------------------------------------------------------------
         */
        
        /**
         * Gets the body of the message.
         *
         * @return StreamInterface
         */
        public function getBody()
        {
            return $this->body;
        }
        
        /**
         * Returns an instance with the specified message body.
         *
         * @param StreamInterface $body
         * @return static
         */
        public function withBody(StreamInterface $body)
        {
            $clone = clone $this;
            $clone->body = $body;
            return $clone;
        }
    }