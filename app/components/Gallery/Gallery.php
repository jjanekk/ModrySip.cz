<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 12.2.14
 * Time: 18:27
 */

namespace components\Gallery;


use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Http\Request;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Nextras\Forms\Rendering\Bs3FormRenderer;
use Orm\Entity\Action;
use Orm\Entity\File;
use Orm\Repository\Actions;
use Orm\Repository\Files;
use Orm\Repository\Galleries;

class Gallery extends Control{

    /** @var \Orm\Entity\Gallery */
    public $gallery;

    /** @var  Action */
    public $action;

    /** @var  Galleries */
    protected $galleries;

    /** @var  Files */
    protected $files;

    /** @var  Actions */
    protected $actions;

    /** @var  UploadFiles */
    protected $uploadFiles;

    public $isAdmin = true;


    function __construct(\Orm\Repository\Files $files, \Orm\Repository\Galleries $gs, \Orm\Repository\Actions $ac, UploadFiles $up){
        $this->actions = $ac;
        $this->galleries = $gs;
        $this->uploadFiles = $up;
        $this->files = $files;
    }

    protected function createComponentAddImage(){
        $form = new Form();
        $form->setRenderer(new Bs3FormRenderer());

        $form->addGroup('');

        for($i = 0; $i< 5; $i++){
            $form->addUpload('file_'.$i);
        }

        $form->addGroup("Fotogalerie");
        $form->addTextArea('description', 'Popis galerie');

        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = $this->saveImage;

        return $form;
    }

    public function saveImage(Form $form){
        $values = $form->values;
        try{
            for($i = 0; $i< 5; $i++){
               $val = 'file_' . $i;
               $this->prepareFile($values->{$val});
            }
        }catch (\Exception $ex){
            $form->addError('Soubor se nepodařilo uložit. ' . $ex->getMessage());
            return;
        }
        $this->gallery->description = $values->description;

        if(!empty($values->description)){
            $this->gallery->description = $values->description;
        }

        $this->galleries->persist($this->gallery);

        $this->flashMessage('Soubor uložen');
        $this->redirect('this');
    }

    protected function prepareFile(\Nette\Http\FileUpload $file){
        if(!$file->isImage()) return false;
        $this->uploadFiles->uploadFile($file, $this->gallery->dir);
        $fe = $this->uploadFiles->createEntityFile($this->gallery->id);
        //$this->files->persist($fe);
    }

    public function render($gallery = null, $isAdmin = false){
        if($this->gallery)
            $this->action = $this->gallery->getAction();
        if($gallery)
            $this->gallery = $gallery;
        else
            $gallery = $this->gallery;

        if($gallery){
            $this['addImage']->setValues($gallery->toArray());
        }

        $template = $this->template;
        $template->setFile(__DIR__ . '/gallery.latte');
        $template->isAdmin = $isAdmin;

        if($gallery == null){
            $g = new \Orm\Entity\Gallery();
            $g->dir = Strings::webalize($this->action->name . Strings::random(5));
            $this->uploadFiles->createDir($g->dir);
            $this->galleries->persist($g);
            $this->action->setGallery($g);
            $this->actions->persist($this->action);
            $gallery = $g;
        }
        if(!$this->uploadFiles->exitsDir($gallery->dir)){
            $this->uploadFiles->createDir($gallery->dir);
        }

        if($this->files && isset($this->gallery)){
            $this->template->files = $this->files->getByGallery($this->gallery->id);
            $this->template->dir = 'Galleries/' . $this->gallery->dir;
        }

        $ip = $this->presenter->context->getService("httpRequest")->getRemoteAddress();
        if($ip == "::1" || $ip == "127.0.0.1" || $ip == "localhost"){

        }else{
            $this->template->basePath = 'http://www.modrysip.cz';
        }
        $template->render();
    }

    function handleRemove($id){
        $file = $this->files->get($id);

        if($file){
            $this->files->delete($file);
            $this->presenter->flashMessage("Soubor smazán.");
        }
        $this->redirect('this');
    }


    function handleHomeImage($id){
        /** @var File $file */
        $file = $this->files->get($id);
        $file->isViewImage = true;
        $this->files->removeViewImage();
        $this->files->persist($file);
    }


}