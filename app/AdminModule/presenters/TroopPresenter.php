<?php
namespace App\AdminModule;


use AdminModule\Forms\TroopForm;
use components\FileGrid\TroopGrid;
use Nette\Application\UI\Form;
use Nextras\Forms\Rendering\Bs3FormRenderer;
use Orm\Entity\Scout_troop;
use Orm\Repository\Scout_troops;

class TroopPresenter extends AdminPresenter{

    /** @persistent int */
    public $id;

    /** @var \Orm\Repository\Scout_troops @inject */
    public $scout_troops;

    /** @var  Scout_troop */
    public $scout_troop;


    protected function startup()
    {
        parent::startup();
        $this->breadcrumb->setData(array('Troop:default' => 'Skautské oddíly', 'Troop:edit' => 'Editace'));
    }

    public function actionEdit($id = null){
        $this->scout_troop = $this->scout_troops->get($id);
        if(!$this->scout_troop && $id){
            $this->error('404');
        }
    }

    public function renderEdit($id = null){
        if($this->scout_troop){
            $this['editForm']->values = $this->scout_troop->toArray();
        }
    }

    public function handleRemove($id = null){
        $en = $this->scout_troops->get($id);
        $this->scout_troops->delete($en);
        $this->flashMessage('Smazáno.');
        $this->redirect('this');
    }

    protected function createComponentTroopGrid(){
        $grid = new TroopGrid($this->scout_troops);
        $grid->setDataSourceCallback($this->getDataSource);
        $grid->setPagination(25, $this->getDataSourceSum);

        return $grid;
    }

    protected function createComponentEditForm(){
        $form = new TroopForm();
        $form->build();
        $form->setRenderer(new Bs3FormRenderer());
        $form->onSuccess[] = $this->saveForm;

        return $form;
    }

    protected  function prepareDataSource($filter, $order)
    {
        $filters = array();
        $selection = $this->scout_troops->getSelection();

        foreach ($filter as $k => $v) {
            $selection->where($k, $v);
        }

        if(is_array($order) && count($order) == 2){
            $selection->order($order);
        }else{
            $selection->order('level ASC');
        }

        return $this->scout_troops->createCollectionFromSection($selection);
    }

    public function saveForm(Form $form){
        $values = $form->values;

        $troop = $this->scout_troop;
        if(!$this->scout_troop){
            $troop = new Scout_troop();
        }

        $troop->name = $values->name;
        $troop->color = $values->color;
        $troop->description = $values->description;
        $troop->showInList = $values->showInList;
        $troop->level = (int)$values->level;
        $troop->home_page_description = $values->home_page_description;
        $success = false;

        try{
            $this->scout_troops->persist($troop);
            $this->flashMessage('Uloženo.');
            $success = true;
        }catch (\Exception $ex){
            $this->flashMessage('Omlouváme se, ale nastala chyba a nepodařilo se provést uložení: ' . $ex->getMessage());
        }

        if($success)$this->redirect('this', $troop->id);
    }

} 