<?php 
    
    /**
     * HeadersTrait
     * 
     * This trait sets HTTP headers.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http
     */

    namespace Embryo\Http\Message\Traits;
    
    trait HeadersTrait
    { 
        /** 
         * Sets HTTP headers from $_SERVER. 
         *
         * @param array $server 
         * @return self
         */
        protected function setHeaders(array $server)
        {
            $headers = [];
            foreach ($server as $key => $value) {
                
                if(substr($key, 0, 5) == 'HTTP_') {
                    
                    $name = $this->setHeaderName($key);
                    $headers[$name] =  [
                        'original' => $key, 
                        'values'   => [$value]
                    ];
                }

            }
            return $headers;
        }

        /**
         * Sets case-insensitive header name.
         *
         * @param string $key 
         * @return string 
         */
        protected function setHeaderName($key)
        {
            $key = strtr(strtolower($key), '_', '-');
            if (strpos($key, 'http-') === 0) {
                $key = substr($key, 5);
            }
            return $key;
        }

        /**
         * Sets Host header if preserve host is true.
         * 
         * @param string $headerHost
         * @param string $host 
         * return string[][]
         */
        protected function setPreserveHost($headerHost, $host)
        {
            $header = [];
            if ($host !== '' && $headerHost === '') {
                $header = [
                    'original' => 'HTTP_HOST', 
                    'values'   => [$host]
                ];
            }
            return $header;
        }

        /**
         * Sets Host header if preserve host is false.
         * 
         * @param string $host 
         * return string[][]
         */
        protected function setNotPreserveHost($host)
        {
            $header = [];
            if ($host !== '') {
                $header = [
                    'original' => 'HTTP_HOST', 
                    'values'   => [$host]
                ];
            }
            return $header;
        }
    }