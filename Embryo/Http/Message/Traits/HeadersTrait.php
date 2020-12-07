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
         * @var array $special
         */
        private $special = [
            'CONTENT_TYPE',
            'CONTENT_LENGTH',
            'PHP_AUTH_USER',
            'PHP_AUTH_PW',
            'PHP_AUTH_DIGEST',
            'AUTH_TYPE'
        ];

        /** 
         * Sets HTTP headers from $_SERVER. 
         *
         * @param array $server 
         * @return array
         */
        protected function setHeaders(array $server)
        {
            $headers = [];

            if (!isset($server['authorization'])) {
                $allHeaders = array_change_key_case(getallheaders(), CASE_LOWER);
                if (isset($allHeaders['authorization'])) {
                    $name = $this->setHeaderName('HTTP_AUTHORIZATION');
                    $headers[$name] =  [
                        'original' => 'HTTP_AUTHORIZATION', 
                        'values'   => [$allHeaders['authorization']]
                    ];
                }
            }
            
            foreach ($server as $key => $value) {
                if(in_array($key, $this->special) || substr($key, 0, 5) == 'HTTP_') {
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
        protected function setHeaderName(string $key)
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
         * @return array
         */
        protected function setPreserveHost(string $headerHost, string $host)
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
         * @return array
         */
        protected function setNotPreserveHost(string $host)
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