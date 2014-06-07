<?php

namespace App\AdminModule;
use AdminModule\Forms\ActionForm;
use components\ActionGrid\ActionGrid;
use components\Gallery\Gallery;
use Nette\DateTime;
use Orm\Entity\Action;
use Orm\Repository\Actions;
use Orm\Repository\Scout_troops as Sts;


class ActionPresenter extends AdminPresenter
{
    /** @persistent int */
    public $id;

    /** @var  Actions */
    public $actions;

    /** @var  Sts */
    public $scout_troops;

    /** @var  Action */
    public $action;

    /** @var  \components\Gallery\Gallery  @inject*/
    public $gallery;


    function __construct(\Orm\Repository\Actions $actions, Sts $sts){
        $this->actions = $actions;
        $this->scout_troops = $sts;
    }

    protected function startup()
    {
        parent::startup();
        $this->breadcrumb->setData(array('Action:default' => 'Akce a reportáže','Action:edit' => 'Editace'));
    }

    public function actionEdit($id = null){
        $this->action = $this->actions->get($id);
        if(!$this->actions && $id){
            $this->error('404');
        }

        if(!$this->getUser()->isAllowed('create') && $id == null){
            $this->redirect('default');
        }
    }

    public function actionGallery($id = null){
        $this->action = $this->actions->get($id);
        if(!$this->actions && $id){
            $this->error('404');
        }
    }

    public function renderGallery($id = null){
        $this->template->gallery = $this->action->getGallery();
    }

    public function renderEdit($id = null){
        if($id){
            $this['editForm']->setValues($this->action->toArray());
        }
    }

    public function handleRemove($id){
        if($el = $this->actions->get($id)){
            $this->actions->delete($el);
        }
        $this->flashMessage('Položka byla vymazána.');
        if(!$this->isAjax())
            $this->redirect('this');
    }

    protected function createComponentTable()
    {
        //@todo factory
        $grid = new ActionGrid();
        $grid->setDataSourceCallback($this->getDataSource);
        $grid->setPagination(25, $this->getDataSourceSum);
        return $grid;
    }

    protected function createComponentGallery(){
        $gallery = $this->gallery;
        $gallery->gallery = $this->action->getGallery();
        $gallery->action = $this->action;

        return $gallery;
    }

    protected function createComponentEditForm(){
        $form = new ActionForm($this->scout_troops);
        $form->onSuccess[] = $this->submitForm;

        return $form;
    }

    protected function prepareDataSource($filter, $order)
    {
        //@todo
        $filters = array();
        $selection = $this->actions->getSelection();

        foreach ($filter as $k => $v) {
            if ($k === 'user'){
                $selection->where('user.nick = ? OR user.first_name = ? OR user.last_name = ?', $v, $v, $v);
            }

            if ($k === 'name'){
                $selection->where('action.name = ? OR :action_has_troop.scout_troop.name = ?', $v, $v);
            }

            if($k == 'scout_troop'){

            }

            try {
                if ($k === 'date'){
                    $v = new DateTime($v);
                    $selection->where('date_from = ? OR date_to = ? OR (date_from >= ? AND date_to <= ?)', $v, $v, $v, $v);
                }
            } catch (Exception $e) {
                // nevalidni datum
            }

        }

        if(is_array($order) && count($order) == 2){
            if($order[0] == 'user'){
                $selection->order('user.nick '. $order[1]);
            }elseif($order[0] == 'name'){
                $selection->order('name ' . $order[1]);
            }elseif($order[0] == 'date'){
                $selection->order('date_from ' . $order[1]);
            }else{
                $order = implode($order, ' ');
                $selection->order($order);
            }

        }else{
            $selection->order('date_from DESC');
        }

        if($selection->count() > 50){
            $selection->limit(50);
        }

        //$selection = $this->actions->getAll()->orderBy('date_from DESC')->limit(100);
        return $this->actions->createCollectionFromSection($selection);
    }

    public function submitForm(ActionForm $form){
        $values = $form->getValues();
        $action = $this->action;
        if(!$action){
            $action = new Action();
        }

        $action->name = $values->name;
        $action->date_from = $values->date_from;
        $action->date_to = $values->date_to;
        $action->action = $values->action;
        $action->report = $values->report;
        $action->setUser($this->activeUserEntity);
        $this->actions->persist($action);

        $troops = $action->getTroops();

        $newArray = array();
        foreach($values->scout_troop as $st){
            $newArray[$st] = $st;
        }

        $values->scout_troop = $newArray;

        foreach($troops as $st){
            if(!in_array($st->id, $values->scout_troop)){
                $action->removeTroop($this->scout_troops->get($st->id));
                unset($values->scout_troop[$st->id]);
            }
        }

        foreach($values->scout_troop as $st){
            $action->addTroop($this->scout_troops->get($st));
        }

        $this->actions->persist($action);
        $this->flashMessage('Položka byla uložena');
        $this->redirect('this', $action->id);
    }

}
