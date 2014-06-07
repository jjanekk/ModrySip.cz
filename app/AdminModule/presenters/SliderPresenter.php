<?php
namespace App\AdminModule;


use BasePresenter;
use components\Gallery\UploadFiles;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Nextras\Forms\Rendering\Bs3FormRenderer;
use Orm\Entity\Scout_troop;
use Orm\Entity\Slider;
use Orm\Repository\Sliders;

class SliderPresenter extends AdminPresenter{

    /** @persistent int */
    public $id;

    /** @persistent */
    public $sliderId;

    /** @persistent */
    public $troopId;

    /** @var \Orm\Repository\Sliders @inject */
    public $sliders;

    /** @var \components\Gallery\UploadFiles @inject */
    public $fileUpload;

    /** @var  Scout_troop */
    protected $selectedTroop;

    /** @var  Slider */
    protected $activeSlide;


    protected function startup()
    {
        parent::startup();
        $this->breadcrumb->setData(array('Slider:default' => 'Slider', 'Slider:slidersBox' => 'Oddíl' , 'Slider:edit' => 'Editace'));
    }

    function renderDefault(){
        $this->template->troops = $this->scout_troops->getAll();
    }

    function actionSlidersBox($troopId){
        $this->selectedTroop = $this->scout_troops->get($troopId);
        $this->sliderId = null;
        if($troopId && !$this->selectedTroop){
            $this->error('404');
        }
    }

    function renderSlidersBox($troopId){
        $this->template->troop = $this->selectedTroop;
        $this->template->slider = $this->sliders->getByTroopId($this->selectedTroop->id);
    }

    function actionEdit($sliderId = null){
        $this->activeSlide = $this->sliders->get($sliderId);
        if($sliderId && !$this->activeSlide){
            $this->error('404');
        }
    }

    function renderEdit($sliderId = null){
        if($this->activeSlide)
            $this['edit']->setValues($this->activeSlide->toArray());
    }

    function handleRemove($id){
        $activeSlide = $this->sliders->get($id);
        if($activeSlide){
            $this->sliders->delete($activeSlide);
            $this->flashMessage("Smazáno.");
        }
        $this->redirect('this', $this->troopId);
    }

    function createComponentEdit(){
        $form = new Form();
        $form->addText('name', 'Název');
        $form->addHidden('troop_id')
            ->setValue($this->id);
        $form->addUpload('image', 'Obázek')
            ->addRule(Form::IMAGE);
        $form->addSubmit('save');
        $form->onSuccess[] = $this->edit;

        return $form;
    }

    function edit(Form $form){
        $values = $form->values;
        $el = null;
        if($this->sliderId){
            $el = $this->sliders->get($this->sliderId);
        }else{
            $el = new Slider();
        }

        $el->name = $values->name;
        $el->scout_troop_id = (int)$this->troopId;;
        /** @var \Nette\Http\FileUpload $file */
        $file = $values->image;
        $image = $file->toImage();

        $this->fileUpload->uploadFile($file, 'slider', true, false, array(930, 230));
        $fe = $this->fileUpload->createEntityFile();
        $el->setFile($fe);

        $this->sliders->persist($el);
        $this->flashMessage('Uloženo');
        $this->redirect('this', $el->id);
    }

} 