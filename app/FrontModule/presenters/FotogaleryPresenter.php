<?php
namespace App\FrontModule;


use App\BasePresenter;
use components\Gallery\Gallery;
use Nette\DateTime;
use Nette\Application\UI\Form;
use Orm\Repository\Galleries;
use YetORM\EntityCollection;

class FotogaleryPresenter extends FrontPresenter{

    /** @persistent int */
    public $id;

    /** @var \Orm\Repository\Galleries @inject */
    public $galleries;

    /** @var  \components\Gallery\Gallery @inject */
    public $gallery;

    protected $filter;

    /** @var  EntityCollection */
    protected $galleryData;



    function renderDefault(){
        if(!$this->filter){
            if($this->activeTroop->id != 10)
                $this->galleryData = $this->actions->getByTroop($this->activeTroop->id);
            else
                $this->galleryData = $this->actions->getAll();
            $this->galleryData->orderBy('date_from DESC');
        }
        $this->template->actions = $this->galleryData;
    }

    function actionGallery($id){
        if(!$this->galleries->get($id))
            $this->error('404');
    }

    function renderGallery($id){
        $action = $this->galleries->get($id)->getAction();
        if($action)
            $this->template->name = $this->galleries->get($id)->getAction()->name;
        else
            $this->template->name = "Bez jména";
        $this->template->description = $this->galleries->get($id)->description;
    }

    protected function createComponentGallery(){
        $gallery = $this->gallery;
        $gallery->isAdmin = false;
        $gallery->gallery = $this->galleries->get($this->id);

        return $gallery;
    }

    function createComponentFilterFormGalleries(){
        $form = new Form();

        $form->addSelect('year', 'Rok: ', $this->actions->getYears())
            ->setPrompt('Rok');

        $datetime = new DateTime();
        $year = (int)$datetime->format('Y') + 1;
        $years = array();
        for($i = 2005; $i < $year; $i++){
            $years[ $i . "-" . ($i + 1)] = $i . "-" . ($i + 1);
        }

        $form->addSelect('schoolYear', 'Školní rok', $years)
            ->setPrompt('Všechny');

        $form->addSubmit('filter', 'Filtrovat');
        $form->onSubmit[] = $this->filterForm;

        return $form;
    }

    function filterForm(Form $form){
        $values = $form->values;
        $this->filter = $values->schoolYear;
        if($this->filter){
            if($this->activeTroop->id != 10)
                $select = $this->actions->getSelection()->where(':action_has_troop.scout_troop_id', $this->activeTroop->id);
            else
                $select = $this->actions->getSelection();
            $this->galleryData = $this->actions->createCollectionFromSection(
                $this->actions->getBySchoolYear($this->filter, $select));
        }
    }


} 