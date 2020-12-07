<?php 
    
    /**
     * Uri
     * 
     * PSR-7 implementation for creating a URI.
     *
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http 
     * @link   https://github.com/php-fig/http-message/blob/master/src/UriInterface.php
     */
     
    namespace Embryo\Http\Message;
    
    use Embryo\Http\Message\Traits\UriTrait;
    use InvalidArgumentException;
    use Psr\Http\Message\UriInterface;
    
    class Uri implements UriInterface 
    { 
        use UriTrait;

        /**
         * @var string $scheme 
         */
        protected $scheme;
        
        /**
         * @var string $user 
         */
        protected $user;
        
        /**
         * @var string|null $pass
         */
        protected $pass;
        
        /**
         * @var string $host 
         */
        protected $host;
        
        /**
         * @var int|null $port 
         */
        protected $port;
        
        /**
         * @var string $path 
         */
        protected $path;
        
        /**
         * @var string $query
         */
        protected $query;
        
        /**
         * @var string $fragment
         */
        protected $fragment;
        
        /**
         * Creates a new URI from string using a parse_url function.
         * 
         * @param string $uri 
         * @throws InvalidArgumentException
         */
        public function __construct($uri)
        {
            if (!is_string($uri)) {
                throw new InvalidArgumentException('Uri must be a string');
            }

            /** @var string[] */
            $parts = parse_url($uri) ? parse_url($uri) : [];

            $this->scheme   = isset($parts['scheme']) ? $this->filterScheme($parts['scheme']) : '';
            $this->user     = isset($parts['user']) ? $parts['user'] : '';
            $this->pass     = isset($parts['pass']) ? $parts['pass'] : '';
            $this->host     = isset($parts['host']) ? $this->removePortFromHost($parts['host']) : '';
            $this->port     = isset($parts['port']) ? $this->filterPort(intval($parts['port'])) : null;
            $this->path     = isset($parts['path']) ? $parts['path'] : '/';
            $this->query    = isset($parts['query']) ? $this->filterQuery($parts['query']) : '';
            $this->fragment = isset($parts['fragment']) ? $parts['fragment'] : '';
        }

        /**
         * Returns the string representation as a URI reference.
         * 
         * @return string
         */
        public function __toString()
        {
            $scheme    = $this->getScheme();
            $authority = $this->getAuthority();
            $path      = '/' . ltrim($this->getPath(), '/');
            $query     = $this->getQuery();
            $fragment  = $this->getFragment();

            return ($scheme ? $scheme . ':' : '').($authority ? '//' . $authority : '').$path.($query ? '?' . $query : '').($fragment ? '#' . $fragment : '');
        }

        /**
         * ------------------------------------------------------------
         * SCHEME
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves the scheme component of the URI.
         * 
         * If no scheme is present, this method return an empty string.
         *
         * @return string 
         */
        public function getScheme()
        {
            return $this->scheme;
        }

        /**
         * Returns an instance with the specified scheme.
         * 
         * @param string $scheme 
         * @return static
         * @throws InvalidArgumentException
         */
        public function withScheme($scheme)
        {
            $clone = clone $this;
            $clone->scheme = $this->filterScheme($scheme);
            return $clone;
        }

        /**
         * ------------------------------------------------------------
         * AUTHORITY / USERINFO
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves the authority component of the URI.
         * 
         * If no authority information is present, this method return an empty
         * string.
         * 
         * @return string
         */
        public function getAuthority()
        {
            $userInfo = $this->getUserInfo();
            $host = $this->getHost();
            $port = $this->getPort();

            return ($userInfo ? $userInfo . '@' : '') . $host . ($port !== null ? ':' . $port : '');
        }
        
        /**
         * Retrieves the user information component of the URI.
         *
         * If no user information is present, this method return an empty
         * string.
         * 
         * @return string
         */
        public function getUserInfo()
        {
            return $this->user . ($this->pass ? ':' . $this->pass : '');
        }

        /**
         * Returns an instance with the specified user information.
         * 
         * @param string $user 
         * @param null|string $password 
         * @return static
         * @throws InvalidArgumentException
         */
        public function withUserInfo($user, $password = null)
        {
            if (!is_string($user)) {
                throw new InvalidArgumentException('Uri user must be a string');
            }

            if (!is_null($password) && !is_string($password)) {
                throw new InvalidArgumentException('Uri password must be a string or null');
            }

            $clone = clone $this;
            $clone->user = $user;
            $clone->pass = $password;
            return $clone;
        }

        /**
         * ------------------------------------------------------------
         * HOST
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves the host component of the URI.
         *
         * If no host is present, this method MUST return an empty string.
         *
         * @return string
         */
        public function getHost()
        {
            return $this->host;
        }

        /**
         * Returns an instance with the specified host.
         * 
         * @param string $host 
         * @return static
         * @throws InvalidArgumentException
         */
        public function withHost($host)
        {
            if(!is_string($host)){
                throw new InvalidArgumentException('Uri host must be a string');
            }

            $clone = clone $this;
            $clone->host = $this->removePortFromHost($host);
            return $clone;
        }

        /**
         * ------------------------------------------------------------
         * PORT
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves the port component of the URI.
         *
         * If a port is present, and it is non-standard for the current scheme,
         * this method MUST return it as an integer. If the port is the standard port
         * used with the current scheme, this method return null.
         *
         * @return null|int
         */
        public function getPort()
        {
            return ($this->scheme === 'http' && $this->port === 80) || ($this->scheme === 'https' && $this->port === 443) ? null : $this->port;
        }

        /**
         * Returns an instance with the specified port.
         * 
         * @param null|int $port
         * @return static
         * @throws InvalidArgumentException
         */
        public function withPort($port)
        {
            $port = $this->filterPort($port);
            $clone = clone $this;
            $clone->port = $port;
            return $clone;
        }
        
        /**
         * ------------------------------------------------------------
         * PATH
         * ------------------------------------------------------------
         */

        /** 
         * Retrieves the path component of the URI.
         *
         * This method returns "/" string if path is empty.
         *
         * @return string 
         */
        public function getPath()
        {
            return $this->path;
        }

        /**
         * Returns an instance with the specified path.
         * 
         * @param string $path
         * @return static
         * @throws InvalidArgumentException
         */
        public function withPath($path)
        {
            if(!is_string($path)){
                throw new InvalidArgumentException('Uri path must be a string');
            }

            $clone = clone $this;
            $clone->path = ($path == '') ? '/' : $path;
            return $clone;
        }
        
        /**
         * ------------------------------------------------------------
         * QUERY
         * ------------------------------------------------------------
         */

        /**
         * Retrieves the query string of the URI.
         * 
         * @return string
         */
        public function getQuery()
        {
            return $this->query;
        }

        /**
         * Returns an instance with the specified query string.
         * 
         * If no query string is present, this method returns an empty string.
         * The leading "?" character is not part of the query and must not be
         * added.
         * 
         * @param string $query
         * @return static
         * @throws InvalidArgumentException
         */
        public function withQuery($query)
        {
            $clone = clone $this;
            $clone->query = $this->filterQuery($query);
            return $clone;
        }

        /**
         * ------------------------------------------------------------
         * FRAGMENT
         * ------------------------------------------------------------
         */
        
        /**
         * Retrieves the fragment component of the URI.
         *
         * If no fragment is present, this method returns an empty string.
         * 
         * @return string
         */
        public function getFragment()
        {
            return $this->fragment;
        }
        
        /**
         * Returns an instance with the specified URI fragment.
         * 
         * @param string $fragment
         * @return static
         */
        public function withFragment($fragment)
        {
            $clone = clone $this;
            $clone->fragment = $fragment;
            return $clone;
        } 
    }