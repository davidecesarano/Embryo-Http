<?php 

    /**
     * StreamFactory
     * 
     * PSR-17 factory for creating stream.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http 
     * @see    https://github.com/http-interop/http-factory/blob/master/src/StreamFactoryInterface.php
     */

    namespace Embryo\Http\Factory;

    use Embryo\Http\Message\Stream;
    use Psr\Http\Message\StreamInterface;
    use Interop\Http\Factory\StreamFactoryInterface;

    class StreamFactory implements StreamFactoryInterface
    {
        /**
         * Creates a new stream from a string.
         *
         * @param string $content
         * @return StreamInterface
         */
        public function createStream($content = ''): StreamInterface
        {
            $resource = fopen('php://temp', 'rw+');
            $stream = $this->createStreamFromResource($resource);
            $stream->write($content);
            return $stream;
        }

        /**
         * Creates a stream from an existing file.
         *
         * @param string $filename
         * @param string $mode
         * @return StreamInterface
         */
        public function createStreamFromFile($filename, $mode = 'r'): StreamInterface
        {
            $resource = fopen($file, $mode);
            return $this->createStreamFromResource($resource);
        }

        /**
         *
         * Creates a new stream from an existing resource.
         *
         * @param resource $resource
         * @return StreamInterface
         */
        public function createStreamFromResource($resource): StreamInterface
        {
            return new Stream($resource);
        }
    }