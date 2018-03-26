<?php 

    /**
     * UploadedFile 
     * 
     * PSR-7 implementation a file uploaded through an HTTP request.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http 
     * @see    https://github.com/php-fig/http-message/blob/master/src/UploadedFileInterface.php
     */

    namespace Embryo\Http\Message;

    use Embryo\Http\Factory\StreamFactory;
    use InvalidArgumentException;
    use Psr\Http\Message\{StreamInterface, UploadedFileInterface};
    use RuntimeException;

    class UploadedFile implements UploadedFileInterface
    {
        /**
         * @var resource|string $file
         */
        private $file;

        /**
         * @var int $size
         */
        private $size;

        /**
         * @var int $error
         */
        private $error;

        /**
         * @var string|null $clientFilename
         */
        private $clientFilename;

        /**
         * @var string|null $clientMediaType
         */
        private $clientMediaType;

        /**
         * @var bool $moved
         */
        private $moved = false;

        /**
         * Creates a new UploadedFile instance.
         * 
         * @param resource|string $file 
         * @param int|null $size 
         * @param int $error 
         * @param string|null $clientFilename 
         * @param string|null $clientMediaType
         */
        public function __construct($file, $size = null, $error = \UPLOAD_ERR_OK, $clientFilename = null, $clientMediaType = null)
        {
            if (!is_string($file) && !is_resource($file)) {
                throw new InvalidArgumentException('File must be a string or resource');
            }

            $this->file            = is_string($file) ? $file : (new StreamFactory)->createStreamFromResource($file);  
            $this->size            = $size;
            $this->error           = $error; 
            $this->clientFilename  = $clientFilename;
            $this->clientMediaType = $clientMediaType;
            $this->moved           = false;
        }

        /**
         * Retrieves a stream representing the uploaded file.
         *
         * This method returns a StreamInterface instance, representing the
         * uploaded file.
         *
         * @return StreamInterface
         * @throws RuntimeException
         */
        public function getStream(): StreamInterface
        {
            if ($this->moved) {
                throw new RuntimeException(sprintf('Uploaded file %1s has already been moved', $this->clientFilename));
            }

            $file = (is_string($this->file)) ? $this->file : $this->file->getMetadata('uri');
            $stream = (new StreamFactory)->createStreamFromFile($file, 'r');
            return $stream;
        }

        /**
         * Moves the uploaded file to a new location.
         * 
         * @see http://php.net/is_uploaded_file
         * @see http://php.net/move_uploaded_file
         * @param string $targetPath
         * @throws InvalidArgumentException
         * @throws RuntimeException
         */
        public function moveTo($targetPath)
        {
            if ($this->moved) {
                throw new RuntimeException('Uploaded file already moved');
            }

            if (!is_string($targetPath) || $targetPath === '') {
                throw new InvalidArgumentException('Target path must be a non empty string');
            }

            if (!is_writable(dirname($targetPath))) {
                throw new InvalidArgumentException('Target path is not writable');
            }
            
            if (is_string($this->file)) {

                if (php_sapi_name() == 'cli') {
                    
                    if (!rename($this->file, $targetPath)) {
                        throw new RuntimeException(sprintf('Error moving uploaded file %1s to %2s', $this->clientFilename, $targetPath));
                    }
                    $this->moved = true;

                } else {
                    
                    if (!move_uploaded_file($this->file, $targetPath)) {
                        throw new RuntimeException(sprintf('Error moving uploaded file %1s to %2s', $this->clientFilename, $targetPath));
                    }
                    $this->moved = true;

                }

            } else {

                $file = $this->file->getMetadata('uri');
                if (!copy($file, $targetPath)) {
                    throw new RuntimeException(sprintf('Error moving uploaded file %1s to %2s', $this->clientFilename, $targetPath));
                }
                $this->moved = true;

            }

            if (!$this->moved) {
                throw new RuntimeException(sprintf('Uploaded file could not be moved to %s', $targetPath));
            }
        }

        /**
         * Retrieves the file size.
         * 
         * @return int|null
         */
        public function getSize()
        {
            return $this->size;
        }

        /**
         * Retrieves the error associated with the uploaded file.
         * 
         * @return int
         */
        public function getError()
        {
            return $this->error;
        }

        /**
         * Retrieves the filename sent by the client.
         * 
         * @return string|null
         */
        public function getClientFilename()
        {
            return $this->clientFilename;
        }

        /**
         * Retrieves the media type sent by the client.
         * 
         * @return string|null
         */
        public function getClientMediaType()
        {
            return $this->clientMediaType;
        }
    }