<?php 

    /**
     * ServerRequest
     * 
     * PSR-7 implementation of an incoming, server-side HTTP request.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http 
     * @see    https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-17-http-factory.md
     */

    namespace Embryo\Http\Message;

    use Embryo\Http\Message\Request;
    use Psr\Http\Message\{ServerRequestInterface, UriInterface};

    class ServerRequest extends Request implements ServerRequestInterface
    {
        /**
         * @var array $serverParams
         */
        protected $serverParams = [];
        
        /**
         * @var array $cookies
         */
        protected $cookies = [];
        
        /**
         * @var array $queryParams
         */
        protected $queryParams = [];
        
        /**
         * @var array $uploadedFiles
         */
        protected $uploadedFiles = [];

        /**
         * @var null|array|object
         */
        protected $parsedBody;

        /**
         * @var array $attributes
         */
        protected $attributes = [];

        /**
         * Creates new server-side HTTP request.
         * 
         * @param string $method 
         * @param string|UriInterface $uri 
         * @param array $server
         */
        public function __construct(string $method, $uri, array $server = [])
        {
            $this->serverParams = $server;
            parent::__construct($method, $uri, $server);
        }

        /**
         * ------------------------------------------------------------
         * SERVER
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves server parameters typically derived from PHP's 
         * $_SERVER superglobal.
         *
         * @return array 
         */
        public function getServerParams()
        {
            return $this->serverParams;
        }
        
        /**
         * ------------------------------------------------------------
         * COOKIE
         * ------------------------------------------------------------
         */
        
         /**
          * Retrieves cookie parameters typically derived from PHP's 
          * $_COOKIE superglobal
          * 
          * @return array
          */
        public function getCookieParams()
        {
            return $this->cookies;
        }

        /**
         * Returns an instance with the specified cookies.
         * 
         * @param array $cookies
         * @return static
         */
        public function withCookieParams(array $cookies)
        {
            $clone = clone $this;
            $clone->cookies = $cookies;
            return $clone;
        }
        
        /**
         * ------------------------------------------------------------
         * QUERY
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves query string arguments.
         *
         * Retrieves the deserialized query string arguments, if any.
         *
         * The query params might not be in sync with the URI or server
         * params. If you need to ensure you are only getting the original
         * values, you may need to parse the query string from `getUri()->getQuery()`
         * or from the `QUERY_STRING` server param.
         *
         * @return array
         */
        public function getQueryParams()
        {
            if (!empty($this->queryParams)) {
                return $this->queryParams;
            }

            parse_str($this->uri->getQuery(), $this->queryParams);
            return $this->queryParams;
        }
        
        /**
         * Returns an instance with the specified query string arguments.
         *
         * They may be injected during instantiation, such as from PHP's
         * $_GET superglobal, or may be derived from some other value such as the
         * URI.
         * 
         * @param array $query
         * @return static
         */
        public function withQueryParams(array $query)
        {
            $clone = clone $this;
            $clone->queryParams = $query;
            return $clone;
        }
        
        /**
         * ------------------------------------------------------------
         * UPLOADED FILES
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves normalized file upload data.
         *
         * This method returns upload metadata in a normalized tree, with each leaf
         * an instance of Psr\Http\Message\UploadedFileInterface.
         *
         * @return array
         */
        public function getUploadedFiles()
        {
            return $this->uploadedFiles;
        }
        
        /**
         * Creates a new instance with the specified uploaded files.
         *
         * @param array $uploadedFiles
         * @return static
         */
        public function withUploadedFiles(array $uploadedFiles)
        {
            $clone = clone $this;
            $clone->uploadedFiles = $uploadedFiles;
            return $clone;
        }
        
        /**
         * ------------------------------------------------------------
         * BODY
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves any parameters provided in the request body.
         * 
         * @return null|array|object
         */
        public function getParsedBody()
        {
            return $this->parsedBody;
        }

        /**
         * Returns an instance with the specified body parameters. 
         * 
         * @param null|array|object $data
         * @return static
         * @throws \InvalidArgumentException
         */
        public function withParsedBody($data)
        {
            if (!is_array($data) && !is_object($data) && !is_null($data)) {
                throw new \InvalidArgumentException('The withParsedBody parameter must be an array, an object, or a null');
            }

            $clone = clone $this;
            $clone->parsedBody = $data;
            return $clone;
        }
        
        /**
         * ------------------------------------------------------------
         * ATTRIBUTES
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves attributes derived from the request.
         * 
         * @return array
         */
        public function getAttributes()
        {
            return $this->attributes;
        }

        /**
         * Retrieves a single derived request attribute.
         * 
         * If the attribute has not been previously set, returns
         * the default value as provided.
         * 
         * @param string $name 
         * @param mixed $default 
         * @return mixed
         */
        public function getAttribute($name, $default = null)
        {
            return (isset($this->attributes[$name])) ? $this->attributes[$name] : $default;
        }
        
        /**
         * Returns an instance with the specified derived request attribute.
         *
         * @param string $name The attribute name.
         * @param mixed $value The value of the attribute.
         * @return static
         */
        public function withAttribute($name, $value)
        {
            $clone = clone $this;
            $clone->attributes[$name] = $value;
            return $clone;
        }
        
        /**
         * Returns an instance the removes the specified derived request attribute.
         * 
         * @param string $name 
         * @return static
         */
        public function withoutAttribute($name)
        {
            $clone = clone $this;
            if (isset($this->attributes[$name])) {
                unset($clone->attributes[$name]);
            }
            return $clone;
        }
    }