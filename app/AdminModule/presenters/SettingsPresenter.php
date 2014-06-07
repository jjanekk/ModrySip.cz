<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 19.3.14
 * Time: 19:16
 */

namespace App\AdminModule;


use Nette\Application\UI\Form;
use Orm\Entity\Data;

class SettingsPresenter extends AdminPresenter{

    /** @var  \Orm\Repository\Settings @inject */
    public $settings;

    /** @var \components\Gallery\UploadFiles @inject */
    public $upload;

    /** @var  Data */
    public $data;

    protected function startup(){
        parent::startup();
        $this->breadcrumb->setData(array('Settings:default' => 'Nastavení', 'Settings:logos' => 'Sponzoři'));
    }

    function actionLogos(){
        if($this->data){
            $this['uploadImages']->setValues($this->data->toArray());
        }
    }

    function renderLogos(){
        $this->template->logos = $this->settings->getByTag('logo');
    }

    function createComponentUploadImages(){
        $form = new Form();
        $form->addHidden('id');
        $form->addText('name', 'Adresa');
        $form->addHidden('tag')
            ->setValue('logo');
        $form->addUpload('upload', 'Logo');

        $form->addSubmit('save', 'Uložit');
        $form->onSubmit[] = $this->saveLogo;

        return $form;
    }

    function saveLogo(Form $form){
        $values = $form->values;

        if($values->id){
            $dateItem = $this->settings->get((int)$values->id);
        }else{
            $dateItem = new Data();
        }

        $dateItem->name     = $values->name;
        $dateItem->tag      = $values->tag;

        $this->upload->uploadFile($values->upload, 'logos', true, false, array(150, 150));
        $file = $this->upload->createEntityFile();
        $dateItem->setFile($file);
        $this->settings->persist($dateItem);
        $this->flashMessage('Logo uloženo.');
        $this->redirect('this');
    }

    function handleDelete($dataID){
        $en = $this->settings->get($dataID);
        if($en){
            $this->settings->delete($en);
            $this->flashMessage('Smazáno');
        }

        $this->redirect('this');
    }

    function handleEdit($dataID){
        $en = $this->settings->get($dataID);
        if($en){
            $this['uploadImages']->setValues($en->toArray());
        }
        $this->data = $en;
    }

} 