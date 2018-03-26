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

    use InvalidArgumentException;
    use RuntimeException;
    use Psr\Http\Message\StreamInterface;
    
    class Stream implements StreamInterface 
    {        
        /**
         * @var resource
         */
        protected $stream;
        
        /**
         * @var null|int
         */
        protected $size;
        
        /**
         * @var array
         */
        protected $metadata;
        
        /**
         * @var null|int
         */
        protected $seekable;

        /**
         * @var null|int
         */
        protected $readable;
        
        /**
         * @var null|int
         */
        protected $writable;
        
        /**
         * @var array
         */
        protected static $modes = [
            'readable' => ['r', 'w+', 'r+', 'x+', 'c+', 'rb', 'w+b', 'r+b', 'x+b', 'c+b', 'rt', 'w+t', 'r+t', 'x+t', 'c+t', 'a+'],
            'writable' => ['w', 'w+', 'rw', 'r+', 'x+', 'c+', 'wb', 'w+b', 'r+b', 'x+b', 'c+b', 'w+t', 'r+t', 'x+t', 'c+t', 'a', 'a+']
        ];
        
        /**
         * Creates a new Stream.
         *
         * @param resource $stream 
         * @throws InvalidArgumentException
         */
        public function __construct($stream)
        {
            if (is_resource($stream) === false) {
                throw new InvalidArgumentException('Stream must be a resource');
            } else {

                $this->detach();
                $this->stream   = $stream;
                $this->metadata = stream_get_meta_data($stream);
                $this->seekable = $this->metadata['seekable'];
                $this->readable = in_array($this->metadata['mode'], self::$modes['readable']);
                $this->writable = in_array($this->metadata['mode'], self::$modes['writable']);  
                
            }
        }
        
        /**
         * Reads all data from the stream into a string, from the beginning to end.
         * 
         * @return string
         */
        public function __toString()
        {
            if (is_resource($this->stream) === false) {
                return '';
            }

            try {
                $this->rewind();
                return $this->getContents();
            } catch (RuntimeException $e) {
                return '';
            }
        }
        
        /**
         * Closes the stream and any underlying resources.
         */
        public function close()
        {
            if (is_resource($stream) === true) {
                fclose($this->stream);
            }
            $this->detach();
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
         * Gets the size of the stream if known.
         *
         * @link http://php.net/manual/en/function.fstat.php
         * @return int|null
         */
        public function getSize()
        {
            if (!$this->size && is_resource($this->stream) === true) {
                
                $stats = fstat($this->stream);
                $this->size = isset($stats['size']) ? $stats['size'] : null;
            
            }
            return $this->size;
        }
        
        /**
         * Returns the current position of the file read/write pointer.
         *
         * @link http://php.net/manual/en/function.ftell.php
         * @return int
         * @throws RuntimeException 
         */
        public function tell()
        {
            $position = ftell($this->stream);
            if (is_resource($this->stream) === false || $position === false) {
                throw new RuntimeException('Unable to determine stream position');
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
            return (is_resource($this->stream) === true) ? feof($this->stream) : true;
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
         * @throws RuntimeException
         */
        public function seek($offset, $whence = SEEK_SET)
        {
            if (!$this->isSeekable() || fseek($this->stream, $offset, $whence) === -1) {
                throw new RuntimeException('Stream is not seekable');
            }
        }
        
        /**
         * Seek to the beginning of the stream.
         *
         * If the stream is not seekable, this method will raise an exception;
         * otherwise, it will perform a seek(0).
         * 
         * @throws RuntimeException
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
         * Writes data to the stream.
         *
         * @param string $string
         * @return int
         * @throws RuntimeException
         */
        public function write($string)
        {   
            $written = fwrite($this->stream, $string);
            if (!$this->isWritable() || $written === false) {
                throw new RuntimeException('Cannot read from non-readable stream');
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
         * Reads data from the stream.
         *
         * @link http://php.net/manual/en/function.fread.php
         * @param int $length
         * @return string 
         * @throws RuntimeException
         */
        public function read($length)
        {
            $string = fread($this->stream, $length);
            if (!$this->isReadable() || $string  === false) {
                throw new RuntimeException('Cannot read from non-readable stream');
            }
            return $string;
        }
        
        /**
         * Returns the remaining contents in a string.
         * 
         * @link http://php.net/manual/en/function.stream-get-contents.php
         * @return string
         * @throws RuntimeException
         */
        public function getContents()
        {
            $contents = stream_get_contents($this->stream);
            if (!$this->isReadable() || $contents === false) {
                throw new RuntimeException('Unable to read stream contents');
            }
            return $contents;
        }
        
        /**
         * Gets stream metadata as an associative array or retrieve a specific key.
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