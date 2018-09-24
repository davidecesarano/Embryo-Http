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