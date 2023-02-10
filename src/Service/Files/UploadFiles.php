<?php

namespace App\Service\Files;
//use App\Repository\UploadFilesRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\File;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadFiles
{
    private $filesystem;
    public function __construct(Filesystem $privateUploadFileSystem)
    //, UploadFilesRepository $fileRepository)
    {
        $this->filesystem = $privateUploadFileSystem;
    }

    public function upload(File $file, $destination) 
    {
        if($file){
            
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = Urlizer::urlize($originalFilename,$separator = '_').'_'.uniqid().'.'.$file->guessExtension();
            $stream = fopen($file->getPathname(), 'r');
            $uploadFile = $this->filesystem->writeStream($newFilename, $stream);
            
            if (is_resource($stream)) {
                fclose($stream);
            }

            return $newFilename;
        }

    }


    public function getFile($file)
    {    
        $response = new StreamedResponse(function() use($file){
            $filePath = $file->getFileName();
            $outputStream = fopen('php://output', 'wb');
            $contents = $this->filesystem->readStream($filePath);
            stream_copy_to_stream($contents, $outputStream);
        });
        $response->headers->set('Content-Type', 'multipart/form-data');
        return $response;

    }
}
