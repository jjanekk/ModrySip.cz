<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 27.1.14
 * Time: 17:28
 */

namespace App\AdminModule;


use AdminModule\Forms\UserForm;
use AdminModule\Forms\UserProfileForm;
use BasePresenter;
use components\ActionGrid\UserGrid;
use components\Gallery\UploadFiles;
use Nette\Application\UI\Form;
use Orm\Entity\User;
use Orm\Entity\User_function;
use Orm\Repository\Files;
use Orm\Entity\File;
use Orm\Repository\Users;

class UserPresenter extends AdminPresenter{
    const USER_PHOTO_DIR_NAME = 'user';

    /** @var  User */
    public $scout;

    /** @persistent int */
    public $id;

    /** @var \Orm\Repository\Users @inject*/
    public $scouts;

    /** @var \Orm\Repository\User_functions @inject*/
    public $user_functions;

    /** @var  \components\Gallery\UploadFiles @inject */
    public $uploadFiles;

    /** @var  \Orm\Repository\Files @inject */
    public $files;

    /** @var  User_function */
    protected $activeFunction;

    //------------------------------------


    protected function startup()
    {
        parent::startup();
        $this->breadcrumb->setData(array('User:default' => 'Uživatelé', 'User:edit' => 'Editace'));
    }

    function beforeRender(){
        $this->template->userEntity = $this->users->get($this->getUser()->id);
    }

    public function actionEdit($id = null){
        $this->scout = $this->scouts->get($id);
        if(!$this->scout && $id){
            $this->error('404');
        }
    }

    public function renderEdit($id){
        if($this->scout){
            $this['editForm']->values = $this->scout->toArray();
        }
    }

    public function renderProfil(){
        $this['editProfileForm']->values = $this->users->get($this->getUser()->id)->toArray();
    }

    public function renderFunction(){
        $this->template->functions = $this->user_functions->getAll();
    }

    protected function createComponentUserGrid(){
        $grid = new UserGrid($this->user_functions);
        $grid->setDataSourceCallback($this->getDataSource);
        $grid->setPagination(40, $this->getDataSourceSum);
        return $grid;
    }

    protected function prepareDataSource($filter, $order)
    {
        $filters = array();
        $selection = $this->scouts->getSelection();

        foreach ($filter as $k => $v) {
            if($k == 'name'){
                $selection->where('nick = ? OR first_name = ? OR last_name = ?', $v, $v, $v);
            }elseif($k == 'scout_troop_id'){
                $selection->where('scout_troop_id = ?', $v);
            }
            elseif($k == 'user_function_id'){
                $selection->where(':user_has_functions.user_function.id = ?', $v);
            } else{
                $selection->where($k, $v);
            }
        }

        if(is_array($order) && count($order) == 2){
            $selection->order($order);
        }else{
            $selection->order('nick ASC');
        }

        return $this->scouts->createCollectionFromSection($selection);
    }

    public function handleRemove($id){
        try{
            $en = $this->scouts->get($id);
            $this->scouts->delete($en);
        }catch (\Exception $ex){
            $this->flashMessage('Smazání se nezdařilo.');
            return;
        }
        $this->flashMessage('Smazáno.');
        $this->redirect('this');
    }

    protected function createComponentEditForm(){
        $form = new UserForm($this->user_functions, $this->scout_troops);
        $form->onSuccess[] = $this->saveUser;

        return $form;
    }

    protected function createComponentEditProfileForm(){
        $form = new UserProfileForm($this->user_functions, $this->scout_troops);
        $form->onSuccess[] = $this->saveUserProfile;

        return $form;
    }

    public function saveUserProfile(Form $form){
        $values = $form->values;

        $en = $this->users->get($this->getUser()->id);

        $en->nick = $values->nick;
        $en->first_name = $values->first_name;
        $en->last_name = $values->last_name;
        $en->email = $values->email;
        $en->phone = $values->phone;
        $en->password = Users::hashPassword($values->password);

        if(isset($values->photo)){
            $enFile = $this->prepareFile($values->photo);
            if(isset($enFile->id)) $en->file_id = $enFile->id;
        }

        if(isset($values->removePhoto)){
            //@todo remove file from server
            $en->file_id = null;
        }

        $this->scouts->persist($en);
        $this->flashMessage('Uloženo.');
        $this->redirect('this');
    }

    public function saveUser(Form $form){

        $values = $form->values;

        $en = $this->scout;
        if(!$en){
            $en = new User();
        }

        $en->nick = $values->nick;
        $en->login = $values->login;

        $en->first_name = $values->first_name;
        $en->last_name = $values->last_name;
        $en->email = $values->email;
        $en->phone = $values->phone;
        $en->active = $values->active;
        $en->role = $values->role;
        $en->scout_troop_id = $values->scout_troop_id;
        //$en->scout_troop_id = $values->scout_troop_id;

        if(isset($values->photo)){
            $enFile = $this->prepareFile($values->photo);
            if(isset($enFile->id)) $en->file_id = $enFile->id;
        }
        if(isset($values->removePhoto) && $values->removePhoto){
            $en->file_id = null;
        }
        $this->scouts->persist($en);
        $functions = $en->getFunctions();

        $newArray = array();
        foreach($values->user_functions as $st){
            $newArray[$st] = $st;
        }

        $values->user_functions = $newArray;

        foreach($functions as $st){
            if(!in_array($st->id, $values->user_functions)){
                $en->removeFunction($this->scouts->get($st->id));
                unset($values->user_functions[$st->id]);
            }
        }

        foreach($values->user_functions as $st){
            $en->addFunction($this->user_functions->get($st));
        }

        $this->scouts->persist($en);
        $this->flashMessage('Uživatel uložen.');
        $this->redirect('this', $en->id);
    }

    protected function prepareFile(\Nette\Http\FileUpload $file){
        if(!$file->isImage()) return false;
        $this->uploadFiles->uploadFile($file, self::USER_PHOTO_DIR_NAME, true, false);
        $fe = new File();
        $n = $this->uploadFiles->getLastUploadFile();
        $fe->surfix = '.' . pathinfo($n, PATHINFO_EXTENSION);
        $fe->file_name = substr($n, 0, -(strlen($fe->surfix)));
        $fe->mime_type = $file->contentType;

        $this->files->persist($fe);
        return $fe;
    }


    function createComponentEditFunction(){
        $form = new Form();
        $form->addHidden('id');
        $form->addText('name', 'Název')
            ->setAttribute('placeholder', 'Název funkce');
        $form->addCheckbox('main', null);
        $form->addCheckbox('troop', null);
        $form->addText('level', "Pořadí")
            ->setAttribute('placeholder', 'Pořadí');
        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = $this->editFunction;

        return $form;
    }

    function editFunction(Form $form){
        $values = $form->values;
        $en = null;
        if(!$values->id){
            $en = new User_function();
        }else{
            $en = $this->user_functions->get($values->id);
        }

        $en->name = $values->name;
        $en->main = $values->main;
        $en->troop = $values->troop;
        $en->level = (int)$values->level;

        $this->user_functions->persist($en);
        $this->flashMessage('Uloženo');
        $this->redirect('this');
    }

    function handleRemoveFunction($functionID){
        $en = $this->user_functions->get($functionID);
        $this->user_functions->delete($en);

        $this->flashMessage('Funkce byla smazána.');
        $this->redirect('this');
    }

    function handleFunctionEdit($functionID){
        $this->activeFunction = $this->user_functions->get($functionID);
        $this['editFunction']->setValues($this->activeFunction->toArray());
        $this->redrawControl('user_form');
    }


} 