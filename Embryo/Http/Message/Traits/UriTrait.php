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

    use InvalidArgumentException;

    trait UriTrait
    {
        /**
         * Validates Uri scheme.
         * 
         * @param string $scheme
         * @return string
         */
        protected function filterScheme($scheme)
        {
            if (!is_string($scheme)) {
                throw new InvalidArgumentException('Uri scheme must be a string');
            }

            $scheme = str_replace('://', '', strtolower($scheme));
            if ($scheme !== 'https' && $scheme !== 'http' && $scheme !== '') {
                throw new InvalidArgumentException('Uri scheme must be one of: "", "https", "http"');
            }
            return $scheme;
        }

        /**
         * Validates Uri port.
         * 
         * @param null|int $port
         */
        protected function filterPort($port)
        {
            if($port && !is_int($port)){
                throw new InvalidArgumentException('Uri port must be an integer or null');
            }
            return $port;
        }

        /**
         * Validates Uri query.
         * 
         * @param string $query
         * @return string
         */
        protected function filterQuery($query)
        {
            if(!is_string($query)){
                throw new InvalidArgumentException('Uri query must be a string');
            }

            $query = ltrim($query, '?');
            return $query;
        }
    }