<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 12.3.14
 * Time: 23:00
 */

namespace App\AdminModule;


use Nette\Utils\Finder;
use Orm\Entity\File;
use Orm\Repository\Files;

class FilesPresenter extends AdminPresenter{

    /** @var  \Orm\Repository\Files @inject*/
    public $files;

    /** @var  \Orm\Repository\Galleries @inject*/
    public $galleries;



    public function renderDefault(){

        $dir = ($this->getHttpRequest()->getUrl()->scriptPath) ;

        foreach (Finder::findFiles('*.jpg')->exclude('*d.jpg')->from('C:\xampp\htdocs\scout\www/Galleries') as $key => $file) {
            $dirName = basename(dirname($key));
            pathinfo($key);
            $info = pathinfo($key);
            $surfix = '.' . $info['extension'];
            $file_name = $info['filename'];
            $mime_type = 'image/jpeg';


            $g = $this->galleries->getByName($dirName);
            if($g){
                $f = new File();
                $f->fotogalery_id = $g->id;
                $f->surfix = $surfix;
                $f->file_name = $file_name;
                $f->mime_type = $mime_type;
                $this->files->persist($f);
            }
        }

        exit;
    }

} 