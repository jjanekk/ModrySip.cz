<?php
namespace App\AdminModule;

use AdminModule\Forms\NewsForm;
use components\FileGrid\NewsGrid;
use Orm\Entity\News;
use Nette\Application\UI\Form;
use Nette\DateTime;
use Orm\Repository\News as NS;
use Orm\Repository\Scout_troops as St;

class NewsPresenter extends AdminPresenter{

    /** @var  */
    protected $news;

    /** @var  News */
    protected $signleNews;


    function __construct(NS $news)
    {
        $this->news = $news;
    }

    protected function startup()
    {
        parent::startup();
        $this->breadcrumb->setData(array('News:default' => 'Novinky', 'News:edit' => 'Editace'));
    }

    public function actionEdit($id = null){
        $this->signleNews = $this->news->get($id);

        if(!$this->signleNews && $id){
            $this->error('404');
        }

        if(!$this->getUser()->isAllowed('create') && $id == null){
            $this->redirect('default');
        }
    }

    public function renderEdit($id = null){
        if($id){
            $this['editForm']->setValues($this->signleNews->toArray());
        }
    }

    protected function createComponentEditForm(){
        $form = new NewsForm($this->scout_troops);
        $form->onSuccess[] = $this->formSubmit;

        return $form;
    }

    function createComponentPageGrid(){

        $grid = new NewsGrid($this->scout_troops);
        $grid->setDataSourceCallback($this->getDataSource);
        $grid->setPagination(25, $this->getDataSourceSum);
        return $grid;
    }

    protected  function prepareDataSource($filter, $order)
    {
        $filters = array();
        $selection = $this->news->getSelection();

        foreach ($filter as $k => $v) {
            if ($k === 'user'){
                $selection->where('user.nick = ? OR user.first_name = ? OR user.last_name = ?', $v, $v, $v);
            }elseif($k == 'date'){
                $v = new DateTime($v);
                $selection->where($k, $v);}
            elseif($k = 'scout_troop_id'){
                $selection->where(':news_has_troop.scout_troop.id = ?', $v);
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

        return $this->news->createCollectionFromSection($selection);
    }

    public function formSubmit(Form $form){
        $values = $form->getValues();
        $singleNews = $this->signleNews;
        if(!$singleNews){
            $singleNews = new News();
        }

        $singleNews->name = $values->name;
        $singleNews->date = new DateTime();
        $singleNews->date_to = $values->date_to;
        $singleNews->text = $values->text;
        $singleNews->setUser($this->users->get($this->getUser()->id));
        $this->news->persist($singleNews);
        $troops = $singleNews->getTroops();

        foreach($troops as $st){
            if(!in_array($st->id, $values->scout_troop)){
                $singleNews->removeTroop($this->scout_troops->get($st->id));
                unset($values->scout_troop[$st->id]);
            }
        }

        foreach($values->scout_troop as $st){
            $singleNews->addTroop($this->scout_troops->get($st));
        }

        $this->news->persist($singleNews);
        $this->flashMessage('Položka byla uložena');
        $this->redirect('this', $singleNews->id);
    }

    public function handleRemove($id){
        if($el = $this->news->get($id)){
            $this->news->delete($el);
        }
        $this->flashMessage('Položka byla vymazána.');
        if(!$this->isAjax())
            $this->redirect('this');
    }

} 