<?php 

    /**
     * RequestTrait
     * 
     * This trait supports Request class for validating method, 
     * setting request target and set uri.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http
     */

    namespace Embryo\Http\Message\Traits;
    
    use Psr\Http\Message\UriInterface;
    use Embryo\Http\Factory\UriFactory;

    trait RequestTrait 
    {
        /**
         * Validates the HTTP method.
         * 
         * @param string $method 
         * @return string
         */
        protected function filterMethod(string $method)
        {
            if (!in_array(strtoupper($method), [
                'CONNECT', 
                'DELETE', 
                'GET', 
                'HEAD', 
                'OPTIONS', 
                'PATCH', 
                'POST', 
                'PUT', 
                'TRACE'
            ])) {
                throw new \InvalidArgumentException('HTTP request method is not valid');
            }
            return strtoupper($method);
        }

        /**
         * Set uri.
         * 
         * @param string|UriInterface $uri
         * @return UriInterface
         */
        protected function setUri($uri): UriInterface
        {
            if ($uri instanceof UriInterface) {
                return $uri;
            } else if (is_string($uri)) {
                return (new UriFactory)->createUri($uri);
            } else {
                throw new \InvalidArgumentException('Uri must be a string or an instance of UriInterface');
            }
        }

        /**
         * Sets request target.
         * 
         * @param string $path 
         * @param string $query 
         * @return string
         */
        protected function setRequestTarget(string $path, string $query)
        {   
            $target = $path;
            if ($target === '') {
                $target = '/';
            }
            
            if ($query != '') {
                $target .= '?'.$query;
            }
            return $target;
        }
    }