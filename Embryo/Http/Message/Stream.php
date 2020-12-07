<?php 
    
    /**
     * Stream
     *
     * PSR-7 implementation that describes a data stream.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http 
     * @see    https://github.com/php-fig/http-message/blob/master/src/StreamInterface.php
     */

    namespace Embryo\Http\Message;

    use Psr\Http\Message\StreamInterface;
    
    class Stream implements StreamInterface 
    {        
        /**
         * @var resource|null $stream
         */
        private $stream;
        
        /**
         * @var null|int $size
         */
        private $size;
        
        /**
         * @var array|null $metadata
         */
        private $metadata;
        
        /**
         * @var bool $seekable
         */
        private $seekable;

        /**
         * @var bool $readable
         */
        private $readable;
        
        /**
         * @var bool $writable
         */
        private $writable;
        
        /**
         * @var array $modes
         */
        private static $modes = [
            'readable' => ['r', 'w+', 'r+', 'x+', 'c+', 'rb', 'w+b', 'r+b', 'x+b', 'c+b', 'rt', 'w+t', 'r+t', 'x+t', 'c+t', 'a+'],
            'writable' => ['w', 'w+', 'rw', 'r+', 'x+', 'c+', 'wb', 'w+b', 'r+b', 'x+b', 'c+b', 'w+t', 'r+t', 'x+t', 'c+t', 'a', 'a+']
        ];
        
        /**
         * Create a new Stream.
         *
         * @param resource $stream 
         * @throws \InvalidArgumentException
         */
        public function __construct($stream)
        {
            if (is_resource($stream) === false) {
                throw new \InvalidArgumentException('Stream must be a resource');
            } else {

                $this->stream   = $stream;
                $this->size     = null;
                $this->metadata = stream_get_meta_data($stream);
                $this->seekable = $this->metadata['seekable'];
                $this->readable = in_array($this->metadata['mode'], self::$modes['readable']);
                $this->writable = in_array($this->metadata['mode'], self::$modes['writable']);  
                
            }
        }
        
        /**
         * Reads all data from the stream into a string, 
         * from the beginning to end.
         * 
         * @return string
         */
        public function __toString()
        {
            try {
                $this->rewind();
                return $this->getContents();
            } catch (\RuntimeException $e) {
                return '';
            }
        }
        
        /**
         * Closes the stream and any underlying resources.
         * 
         * @return void
         */
        public function close()
        {
            if (isset($this->stream) && is_resource($this->stream)) {
                fclose($this->stream);
                $this->detach();
            }
        }
        
        /**
         * Separates any underlying resources from the stream.
         * 
         * @return resource|null
         */
        public function detach()
        {
            $stream         = $this->stream;
            $this->stream   = null;
            $this->size     = null;
            $this->metadata = null;
            $this->seekable = false;
            $this->readable = false;
            $this->writable = false;
            return $stream;
        }
        
        /**
         * Get the size of the stream if known.
         *
         * @link http://php.net/manual/en/function.fstat.php
         * @return int|null
         */
        public function getSize()
        {
            if (!isset($this->stream)) {
                return null;
            }
            
            if (!$this->size) {
                $stats = fstat($this->stream);
                $this->size = $stats && isset($stats[7]) ? $stats[7] : null;
            }
            return $this->size;
        }
        
        /**
         * Returns the current position of the file read/write pointer.
         *
         * @link http://php.net/manual/en/function.ftell.php
         * @return int
         * @throws \RuntimeException 
         */
        public function tell()
        {
            if (!isset($this->stream)) {
                throw new \RuntimeException('Unable to determine stream position');
            }

            $position = ftell($this->stream);
            if ($position === false) {
                throw new \RuntimeException('Unable to determine stream position');
            }
            return $position;
        }
        
        /**
         * Returns true if the stream is at the end of the stream.
         *
         * @link http://php.net/manual/en/function.feof.php
         * @return bool 
         */
        public function eof()
        {
            return !$this->stream || feof($this->stream);
        }
        
        /**
         * Returns whether or not the stream is seekable.
         *
         * @return bool 
         */
        public function isSeekable()
        {
            return $this->seekable;
        }
        
        /**
         * Seek to a position in the stream.
         *
         * @link http://www.php.net/manual/en/function.fseek.php
         * @param int $offset
         * @param int $whence
         * @return void
         * @throws \RuntimeException
         */
        public function seek($offset, $whence = \SEEK_SET)
        {
            if (!$this->stream || !$this->isSeekable() || fseek($this->stream, $offset, $whence) === -1) {
                throw new \RuntimeException('Could not seek in stream');
            }
        }
        
        /**
         * Seek to the beginning of the stream.
         *
         * If the stream is not seekable, this method will raise an exception;
         * otherwise, it will perform a seek(0).
         * 
         * @return void
         * @throws \RuntimeException
         */
        public function rewind()
        {
            $this->seek(0);
        }
        
        /**
         * Returns whether or not the stream is writable.
         *
         * @return bool
         */
        public function isWritable()
        {
            return $this->writable;
        }
        
        /**
         * Write data to the stream.
         *
         * @param string $string
         * @return int
         * @throws \RuntimeException
         */
        public function write($string)
        {   
            if (!$this->stream) {
                throw new \RuntimeException('Cannot write to a non-writable stream');
            }

            $written = fwrite($this->stream, $string);
            if (!$this->isWritable() || $written === false) {
                throw new \RuntimeException('Cannot write to a non-writable stream');
            }
            
            $this->size = null;
            return $written;
        }
        
        /**
         * Returns whether or not the stream is readable.
         *
         * @return bool
         */
        public function isReadable()
        {
            return $this->readable;
        }
        
        /**
         * Read data from the stream.
         *
         * @link http://php.net/manual/en/function.fread.php
         * @param int $length
         * @return string 
         * @throws \RuntimeException
         */
        public function read($length)
        {
            if (!$this->stream) {
                throw new \RuntimeException('Cannot read from non-readable stream');
            }

            $string = fread($this->stream, $length);
            if (!$this->isReadable() || $string  === false) {
                throw new \RuntimeException('Cannot read from non-readable stream');
            }
            return $string;
        }
        
        /**
         * Return the remaining contents in a string.
         * 
         * @link http://php.net/manual/en/function.stream-get-contents.php
         * @return string
         * @throws \RuntimeException
         */
        public function getContents()
        {
            if (!$this->stream) {
                throw new \RuntimeException('Unable to read stream contents');
            }

            $contents = stream_get_contents($this->stream);
            if (!$this->isReadable() || $contents === false) {
                throw new \RuntimeException('Unable to read stream contents');
            }
            return $contents;
        }
        
        /**
         * Get stream metadata as an associative array or retrieve a specific key.
         *
         * The keys returned are identical to the keys returned from PHP's
         * stream_get_meta_data() function.
         * 
         * @link http://php.net/manual/en/function.stream-get-meta-data.php
         * @param string $key
         * @return array|mixed|null
         */
        public function getMetadata($key = null)
        {
            if (is_null($key) === true) {
                return $this->metadata;
            }
            return isset($this->metadata[$key]) ? $this->metadata[$key] : null;
        }
    }