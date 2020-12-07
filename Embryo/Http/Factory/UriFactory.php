<?php 
    
    /**
     * UriFactory
     * 
     * PSR-17 factory for creating a new URI.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http
     * @see    https://github.com/http-interop/http-factory/blob/master/src/UriFactoryInterface.php
     */
    
    namespace Embryo\Http\Factory;

    use Embryo\Http\Message\Uri;
    use Psr\Http\Message\{UriFactoryInterface, UriInterface};

    class UriFactory implements UriFactoryInterface
    {
        /**
         * Creates new Uri from string.
         *
         * @param string $uri
         * @return UriInterface
         */
        public function createUri(string $uri = ''): UriInterface
        {
            return new Uri($uri);
        }

        /**
         * Creates new Uri from array.
         * 
         * @param array $server 
         * @return UriInterface
         * @throws \RuntimeException
         */
        public function createUriFromServer(array $server): UriInterface
        {
            $scheme = (empty($server['HTTPS']) || $server['HTTPS'] === 'off') ? 'http' : 'https';
            $host   = (isset($server['HTTP_HOST'])) ? $server['HTTP_HOST'] : $server['SERVER_NAME'];
            $port   = (isset($server['SERVER_PORT'])) ? (int) $server['SERVER_PORT'] : 80;
            $user   = (isset($server['PHP_AUTH_USER'])) ? $server['PHP_AUTH_USER'] : '';
            $pass   = (isset($server['PHP_AUTH_PW'])) ? $server['PHP_AUTH_PW'] : '';
            
            // path
            $path = parse_url('http://example.com' . $server['REQUEST_URI'], PHP_URL_PATH);
            if (!$path) {
                throw new \RuntimeException('Request URI is malformed');
            }
            $path = rawurldecode($path);

            // query
            $query = (isset($server['QUERY_STRING'])) ? $server['QUERY_STRING'] : '';

            // fragment (not available in $_SERVER)
            $fragment = '';

            $uri = new Uri('');
            $uri = $uri->withScheme($scheme);
            $uri = $uri->withHost($host);
            $uri = $uri->withPort($port);
            $uri = $uri->withUserInfo($user, $pass);
            $uri = $uri->withPath($path);
            $uri = $uri->withQuery($query);
            $uri = $uri->withFragment($fragment);
            return $uri;
        }
    }