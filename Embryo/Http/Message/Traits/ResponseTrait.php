<?php 

    /**
     * ResponseTrait
     * 
     * This trait supports the response class in status and reason
     * phrase validation and append write body method.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http
     */

    namespace Embryo\Http\Message\Traits;

    use InvalidArgumentException;
    
    trait ResponseTrait 
    {
        /**
         * @var array $messages
         */
        protected $messages = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            // Successful 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            444 => 'Connection Closed Without Response',
            451 => 'Unavailable For Legal Reasons',
            499 => 'Client Closed Request',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
            599 => 'Network Connect Timeout Error'
        ];

        /**
         * Validates Http status code.
         * 
         * @param int $status 
         * @return int
         * @throws InvalidArgumentException
         */
        protected function filterStatus($status)
        {
            if (!isset($this->messages[$status])) {
                throw new InvalidArgumentException('Invalid HTTP status code');
            }

            if (!is_integer($status) || $status < 100 || $status > 599) {
                throw new InvalidArgumentException('Invalid HTTP status code');
            }

            return $status;
        }

        /**
         * Validates Http status reason phrase
         * 
         * @param int $status
         * @param string $reasonPhrase
         * @return string
         * @throws InvalidArgumentException
         */
        protected function filterReasonPhrase($status, $reasonPhrase = '')
        {
            if (!is_string($reasonPhrase)) {
                throw new InvalidArgumentException('ReasonPhrase must be a string');
            }

            if ($reasonPhrase === '' && isset($this->messages[$status])) {
                $reasonPhrase = $this->messages[$status];
            }
            
            return $reasonPhrase;
        }

        /**
         * Writes data in response body.
         * 
         * @param string $data 
         * @return $this
         */
        public function write($data)
        {
            $this->getBody()->write($data);
            return $this;
        }
    }