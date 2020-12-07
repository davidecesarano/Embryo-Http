<?php 
    
    /**
     * UploadedFileFactory
     * 
     * PSR-17 factory for uploading file.
     *
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-http  
     * @see    https://github.com/http-interop/http-factory/blob/master/src/UploadedFileFactoryInterface.php
     */
    
    namespace Embryo\Http\Factory;

    use Embryo\Http\Factory\StreamFactory;
    use Embryo\Http\Message\UploadedFile;
    use Psr\Http\Message\{StreamInterface, UploadedFileFactoryInterface, UploadedFileInterface};

    class UploadedFileFactory implements UploadedFileFactoryInterface
    {
        /**
         * Creates a new uploaded file.
         *
         * If a string is used to create the file, a temporary resource will be
         * created with the content of the string.
         *
         * If a size is not provided it will be determined by checking the size of
         * the file.
         * 
         * @param StreamInterface $file
         * @param int|null $size
         * @param int $error
         * @param string|null $clientFilename
         * @param string|null $clientMediaType
         * @return UploadedFileInterface
         * @throws \InvalidArgumentException
         */
        public function createUploadedFile(
            StreamInterface $file, 
            int $size = null, 
            int $error = \UPLOAD_ERR_OK, 
            string $clientFilename = null, 
            string $clientMediaType = null
        ): UploadedFileInterface
        {
            if (!$file->isReadable()) {
                throw new \InvalidArgumentException('Temporany resource must be readable');
            }
            $size = (is_null($size)) ? $file->getSize() : $size;
            return new UploadedFile($file, $size, $error, $clientFilename, $clientMediaType); 
        }

        /**
         * Creates a new uploaded file from server.
         * 
         * @param array $files
         * @return array
         */
        public function createUploadedFileFromServer(array $files)
        {
            $normalized = [];
            foreach ($files as $key => $value) {
                
                if (!isset($value['error'])) {
                    if (is_array($value)) {
                        $normalized[$key] = $this->createUploadedFileFromServer($value);
                    }
                    continue;
                }
                
                $normalized[$key] = [];
                if (!is_array($value['error'])) {

                    if ($value['error'] === 4) {
                        $stream = (new StreamFactory)->createStream('');
                    } else {
                        $stream = (new StreamFactory)->createStreamFromFile($value['tmp_name'], 'r');    
                    }
                    
                    $normalized[$key] = $this->createUploadedFile(
                        $stream,
                        $value['size'],
                        $value['error'],
                        $value['name'],
                        $value['type']
                    );

                } else {

                    $sub = [];
                    foreach ($value['error'] as $id => $error) {
   
                        $sub[$id]['name']     = $value['name'][$id];
                        $sub[$id]['type']     = $value['type'][$id];
                        $sub[$id]['tmp_name'] = $value['tmp_name'][$id];
                        $sub[$id]['error']    = $value['error'][$id];
                        $sub[$id]['size']     = $value['size'][$id];
                        
                        $normalized[$key] = $this->createUploadedFileFromServer($sub);
                    
                    }

                }

            }
            return $normalized;
        }
    }