<?php 

    /**
     * UriTrait
     * 
     * This trait supports Uri class for validating.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http   
     */

    namespace Embryo\Http\Message\Traits;

    trait UriTrait
    {
        /**
         * Validates Uri scheme.
         * 
         * @param string $scheme
         * @throws \InvalidArgumentException
         * @return string
         */
        protected function filterScheme(string $scheme)
        {
            $scheme = str_replace('://', '', strtolower($scheme));
            if ($scheme !== 'https' && $scheme !== 'http' && $scheme !== '') {
                throw new \InvalidArgumentException('Uri scheme must be one of: "", "https", "http"');
            }
            return $scheme;
        }

        /**
         * Remove port from HTTP_HOST
         * if it's presents.
         *
         * @param string $host
         * @return string
         */
        protected function removePortFromHost(string $host)
        {
            $remove = preg_replace('#:(\d+){2,4}#', '', $host);
            return !is_null($remove) ? $remove : '';
        }

        /**
         * Validates Uri port.
         * 
         * @param null|int $port
         * @throws \InvalidArgumentException
         * @return null|int
         */
        protected function filterPort($port)
        {
            if($port && !is_int($port)){
                throw new \InvalidArgumentException('Uri port must be an integer or null');
            }
            return $port;
        }

        /**
         * Validates Uri query.
         * 
         * @param string $query
         * @return string
         */
        protected function filterQuery(string $query)
        {
            $query = ltrim($query, '?');
            return $query;
        }
    }