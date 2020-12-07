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
    use Psr\Http\Message\{StreamInterface, UploadedFileInterface};

    class UploadedFile implements UploadedFileInterface
    {
        /**
         * @var StreamInterface $file
         */
        private $file;

        /**
         * @var int|null $size
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
         * @param StreamInterface $file 
         * @param int|null $size 
         * @param int $error 
         * @param string|null $clientFilename 
         * @param string|null $clientMediaType
         */
        public function __construct(
            StreamInterface $file, 
            int $size = null, 
            int $error = \UPLOAD_ERR_OK, 
            string $clientFilename = null, 
            string $clientMediaType = null
        )
        {
            $this->file            = $file;  
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
         * @throws \RuntimeException
         */
        public function getStream(): StreamInterface
        {
            if ($this->moved) {
                throw new \RuntimeException(sprintf('Uploaded file %1s has already been moved', $this->clientFilename));
            }
            return $this->file;
        }

        /**
         * Moves the uploaded file to a new location.
         * 
         * @see http://php.net/is_uploaded_file
         * @see http://php.net/move_uploaded_file
         * @param string $targetPath
         * @return void
         * @throws \InvalidArgumentException
         * @throws \RuntimeException
         */
        public function moveTo($targetPath)
        {
            if ($this->moved) {
                throw new \RuntimeException('Uploaded file already moved');
            }

            if (!is_string($targetPath) || $targetPath === '') {
                throw new \InvalidArgumentException('Target path must be a non empty string');
            }

            $stream = $this->getStream();
            $file   = $stream->getMetadata('uri');

            if (is_dir($targetPath)) {
                
                if (!is_writable($targetPath)) {
                    throw new \InvalidArgumentException('Target path is not writable');
                }
                $target = rtrim($targetPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->clientFilename;
                
            } else {
                
                if (!is_writable(dirname($targetPath))) {
                    throw new \InvalidArgumentException('Target path is not writable');
                }
                $target = $targetPath;

            }
            
            if (!copy($file, $target)) {
                throw new \RuntimeException(sprintf('Error coping uploaded file %1s to %2s', $this->clientFilename, $targetPath));
            }
        
            $this->moved = true;
            if ($this->moved == false) {
                throw new \RuntimeException(sprintf('Uploaded file could not be moved to %s', $targetPath));
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