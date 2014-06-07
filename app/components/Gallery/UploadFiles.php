<?php

namespace components\Gallery;

use Nette\Image;
use Nette\Object;
use Nette\Utils\Strings;
use Orm\Entity\File;
use Orm\Repository\Files;

class UploadFiles extends Object{

    /** @var  string */
    protected $webDir;

    /** @var  string */
    protected $lastUploadFile;

    /** @var  string */
    protected $lastFile;

    /** @var  Files */
    protected $files;


    function __construct($webDir, \Orm\Repository\Files $files)
    {
        $this->webDir = $webDir;

        $values = array('old-skauti-jestrabi','skauti', 'skautky', 'roveri', 'vedeni-strediska', 'vlcata-a-svetlusky' );
        $this->webDir = str_replace($values, 'www/www', $this->webDir);

        $this->files = $files;
        //dump($webDir);
    }

    public function uploadFile($file, $dirName, $image = true, $gallery = true, $size = array(null, null)){
        $fileName = null;
        $this->lastFile = $file;

        $save = true;

        if($file && $file->isOk() && $file->isImage()){
            $nameBig = Strings::random(15) . $file->getSanitizedName();
            $info = pathinfo($nameBig);

            $nameSmall = $info['filename'] . 'd.' . $info['extension'];

            $path = $this->webDir . DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR;
            //dump($path);
            if(!is_dir($path)){
                mkdir($path, 0777);
            }

            /** @var Image $fileImage */
            $fileImage = $file->toImage();
            $fileImageBig = $file->toImage();

            if($fileImage->getWidth() >= 1000){
                $fileImage->resize(1000, null);
            }

            if($fileImage->getHeight() >= 1000){
                $fileImage->resize(null, 1000);
            }

            $fileImage->save($path . $nameBig);
            if($gallery){
                $fileImageBig->save($path . $nameBig);
                $fileImage = Image::fromFile($path . $nameBig);
                $fileImage->resize(115, 85)->save($path . $nameSmall);
                $fileName = $nameBig;
                $save = false;
            }else{
                if(($size) == 2 && $size[0] != null || $size[1] != null){
                    $fileImage->resize($size[0], $size[1])->save($path . $nameBig);
                }

                $fileName = $nameBig;
            }

            if($save)
                $fileImage->save($path . $nameBig);

        }else{
            //throw new \Exception("File error.");
        }

        $this->lastUploadFile = $fileName;
    }

    public function createEntityFile($gallery_id = null){
        $fe = new File();
        $n = $this->getLastUploadFile();

        $info = pathinfo($n);
        $fe->file_name = $info['filename'];
        $fe->surfix = '.' . $info['extension'];

        $fe->mime_type = $this->lastFile->contentType;
        $fe->fotogalery_id = $gallery_id;
        $this->files->persist($fe);

        return $fe;
    }

    public function getLastUploadFile(){
        return $this->lastUploadFile;
    }

    public function exitsDir($dir){
        return is_dir($this->webDir . DIRECTORY_SEPARATOR . $dir);
    }

    function errorHandle($error){

    }

    public function createDir($dir){
        set_error_handler($this->errorHandle);

        //dump($this->webDir . DIRECTORY_SEPARATOR . $dir); exit;

        mkdir($this->webDir . DIRECTORY_SEPARATOR . $dir, 0777);
    }

} 