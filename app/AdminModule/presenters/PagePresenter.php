<?php
namespace App\AdminModule;


use AdminModule\Forms\PageForm;
use components\FileGrid\PageGrid;
use Nette\Application\UI\Form;
use Nette\DateTime;
use Orm\Entity\Page;
use Orm\Repository\Pages as Pg;
use Orm\Repository\Scout_troops as St;

class PagePresenter extends AdminPresenter{

    /** @persistent int */
    public $id;

    /** @var  Pages */
    protected $pages;

    /** @var  Page */
    protected $page;



    function __construct(Pg $pages)
    {
        $this->pages = $pages;
    }

    protected function startup()
    {
        parent::startup();
        $this->breadcrumb->setData(array('Page:default' => 'Stránky', 'Page:edit' => 'Editace'));
    }

    public function actionEdit($id = null){
        $this->page = $this->pages->get($id);
        if(!$this->page && $id){
            $this->error('404');
        }
        if(!$this->getUser()->isAllowed('create') && $id == null){
            $this->redirect('default');
        }
    }

    public function renderEdit($id = null){
        if($id){
            $this['editForm']->setValues($this->page->toArray());
        }
    }

    public function createComponentEditForm(){
        $form = new PageForm($this->scout_troops);
        $form->onSuccess[] = $this->submitForm;

        return $form;
    }

    function createComponentPageGrid(){

        $grid = new PageGrid($this->scout_troops);
        $grid->setDataSourceCallback($this->getDataSource);
        $grid->setPagination(25, $this->getDataSourceSum);
        return $grid;
    }

    protected  function prepareDataSource($filter, $order)
    {
        //@todo
        $filters = array();
        $selection = $this->pages->getSelection();

        foreach ($filter as $k => $v) {
            if ($k === 'user'){
                $selection->where('user.nick = ? OR user.first_name = ? OR user.last_name = ?', $v, $v, $v);
            }elseif($k == 'date'){
                $v = new DateTime($v);
                $selection->where($k, $v);
            }elseif($k = 'scout_troop_id'){
                    $selection->where(':page_has_troop.scout_troop.id = ?', $v);
            }else{
                $selection->where($k, $v);
            }
        }

        if(is_array($order) && count($order) == 2){
            if($order[0] == 'user'){
                $selection->order('user.nick '. $order[1]);
            }else{
                $order = implode($order, ' ');
                $selection->order($order);
            }

        }else{
            $selection->order('date DESC');
        }

        return $this->pages->createCollectionFromSection($selection);
    }

    public function submitForm(Form $form){
        $values = $form->getValues();
        $page = $this->page;
        if(!$page){
            $page = new Page();
        }

        $page->name = $values->name;
        $page->date = new DateTime();
        $page->content = $values->content;
        $page->template = $values->template;
        $this->pages->persist($page);

        $troops = $page->getTroops();

        foreach($troops as $st){
            if(!in_array($st->id, $values->scout_troop)){
                $page->removeTroop($this->scout_troops->get($st->id));
                unset($values->scout_troop[$st->id]);
            }
        }

        foreach($values->scout_troop as $st){
            $page->addTroop($this->scout_troops->get($st));
        }

        $this->pages->persist($page);
        $this->flashMessage('Položka byla uložena');
        $this->redirect('this', $page->id);
    }

    public function handleRemove($id){
        if($el = $this->pages->get($id)){
            $this->pages->delete($el);
        }
        $this->flashMessage('Položka byla vymazána.');
        if(!$this->isAjax())
            $this->redirect('this');
    }

} 