<?php 

    /**
     * UploadedFile 
     * 
<<<<<<< HEAD
     * PSR-7 implementation a file uploaded through an HTTP request.
=======
     * PSR-7 representation a file uploaded through an HTTP request.
>>>>>>> origin/master
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http 
     * @see    https://github.com/php-fig/http-message/blob/master/src/UploadedFileInterface.php
     */

    namespace Embryo\Http\Message;

    use Psr\Http\Message\{StreamInterface, UploadedFileInterface};
    use RuntimeException;

    class UploadedFile implements UploadedFileInterface
    {
        /**
         * @var StreamInterface|string|resource $file
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
         * @var $clientFilename
         */
        private $clientFilename;

        /**
         * @var $clientMediaType
         */
        private $clientMediaType;

        /**
         * @param StreamInterface|string|resource $file 
         * @param int $size 
         * @param int $error 
         * @param string|null $clientFilename 
         * @param string|null $clientMediaType
         */
        public function __construct($file, $size, $error, $clientFilename = null, $clientMediaType = null)
        {
            $this->file = $file; 
            $this->size = $size;
            $this->error = $error; 
            $this->clientFilename = $clientFilename;
            $this->clientMediaType = $clientMediaType;
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
            return $this->file;
        }

        public function moveTo($targetPath)
        {

        }

        public function getSize()
        {
            return $this->size;
        }

        public function getError()
        {
            return $this->error;
        }

        public function getClientFilename()
        {
            return $this->clientFilename;
        }

        public function getClientMediaType()
        {
            return $this->clientMediaType;
        }
    }