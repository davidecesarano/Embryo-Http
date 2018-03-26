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
    use Interop\Http\Factory\UploadedFileFactoryInterface;
    use InvalidArgumentException;
    use Psr\Http\Message\UploadedFileInterface;

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
         * @param string|resource $file
         * @param int $size
         * @param int $error
         * @param string $clientFilename
         * @param string $clientMediaType
         * @return UploadedFileInterface
         * @throws InvalidArgumentException
         */
        public function createUploadedFile($file, $size = null, $error = \UPLOAD_ERR_OK, $clientFilename = null, $clientMediaType = null): UploadedFileInterface
        {
            if (is_string($file)) {
                $file = (new StreamFactory)->createStream($file);
            }

            if (!$file->isWritable()) {
                throw new InvalidArgumentException('Temporany resource must be writable');
            }

            $size = (is_null($size)) ? $file->getSize() : $size;
            return new UploadedFile($file, $size, $error, $clientFilename, $clientMediaType);
        }

        /**
         * Creates a new uploaded file from array.
         * 
         * @param array $files
         * @return array
         */
        public function createUploadedFileFromArray(array $files)
        {
            $normalized = [];
            foreach ($files as $key => $value) {
                
                if (!isset($value['error'])) {
                    if (is_array($value)) {
                        $normalized[$key] = $this->createUploadedFileFromArray($value);
                    }
                    continue;
                }
                
                $normalized[$key] = [];
                if (!is_array($value['error'])) {

                    $normalized[$key] = $this->createUploadedFile(
                        $value['tmp_name'],
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
                        
                        $normalized[$key] = $this->createUploadedFileFromArray($sub);
                    
                    }

                }

            }
            return $normalized;
        }
    }