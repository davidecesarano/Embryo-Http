<?php 

    /**
     * RequestTrait
     * 
     * This trait supports Request class for validating method and
     * setting request target.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http
     */

    namespace Embryo\Http\Message\Traits;

    use InvalidArgumentException;
    
    trait RequestTrait 
    {
        /**
         * @var array $validMethods
         */
        private $validMethods = [
            'CONNECT', 
            'DELETE', 
            'GET', 
            'HEAD', 
            'OPTIONS', 
            'PATCH', 
            'POST', 
            'PUT', 
            'TRACE'
        ];

        /**
         * Validates the HTTP method
         * 
         * @param string $method 
         * @return string
         */
        protected function filterMethod($method)
        {
            if (!is_string($method)) {
                throw new InvalidArgumentException('HTTP request method must be a string');
            }

            if (!in_array(strtoupper($method), $this->validMethods)) {
                throw new InvalidArgumentException('HTTP request method is not valid');
            }

            return strtoupper($method);
        }

        /**
         * Sets request target
         * 
         * @param string $path 
         * @param string $query 
         * @return string
         */
        protected function setRequestTarget($path, $query)
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