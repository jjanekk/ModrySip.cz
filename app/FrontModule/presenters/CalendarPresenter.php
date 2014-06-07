<?php
namespace App\FrontModule;

use components\Gallery\Gallery;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\DateTime;
use Nette\Utils\Paginator;
use Orm\Repository\Actions;
use Orm\Repository\Scout_troops;
use YetORM\EntityCollection;

class CalendarPresenter extends FrontPresenter{

    /** @var  \components\Gallery\Gallery @inject */
    public $gallery;

    /** @var  \Orm\Repository\Scout_troops @inject */
    public $scout_troops;

    /** @var  EntityCollection */
    public $dataCollection;

    /** @var  string|array */
    public $filter = array();

    /** @var boolean */
    public $filterId = NULL;

    const POSTS_PER_PAGE = 15;


    protected function startup()
    {
        parent::startup();
        $this->filterId = $this->activeTroop->id;
        $actualDate = new DateTime();
        $actualYear = $actualDate->format('Y');
        $actualMonth = $actualDate->format('n');

        if($this->getAction() == 'actions'){
            $this->session->getSection('app')->calendarMonth = $this->session->getSection('app')->calendarMonth?$this->session->getSection('app')->calendarMonth:$actualMonth;
            $this->session->getSection('app')->calendarYear = $this->session->getSection('app')->calendarYear?$this->session->getSection('app')->calendarYear:$actualYear;
        }

        if($this->getAction() == "actions"){
            $this->dataCollection = $this->actions->filter($this->filterId, Actions::ACTION, $this->session->getSection('app')->calendarYear, $this->session->getSection('app')->calendarMonth, $this->session->getSection('app')->schoolYear);
        }elseif($this->getAction() == "report"){
            $this->dataCollection = $this->actions->filter($this->filterId, Actions::REPORT, $this->session->getSection('app')->calendarYear, $this->session->getSection('app')->calendarMonth, $this->session->getSection('app')->schoolYear);
        }

        if($this->getAction() == 'actions' || $this->getAction() == 'report'){
            $count = $this->dataCollection->count();
            if((int)$this->session->getSection('app')->postCount != (int)$count){
                $this->session->getSection('app')->postCount = $count;
            }
        }
    }

    public function beforeRender(){
        parent::beforeRender();
        $this->template->actions = $this->dataCollection;
        $this->template->report = $this->actions->getReports();
        $this->template->scout_troops = $this->collectionOfTroop;
        $this->template->activeId = $this->filterId;
        $this->template->filter = $this->session->getSection('app')->calendarYear || $this->session->getSection('app')->calendarMonth || $this->session->getSection('app')->schoolYear;

        $actualDate = new DateTime();
        $actualYear = $actualDate->format('Y');
        $actualMonth = $actualDate->format('n');
        $date = NULL;

        if($this->getAction() == 'actions'){
            $date = array(
                'year' =>  ($this->session->getSection('app')->calendarYear)?  $this->session->getSection('app')->calendarYear  : $actualYear,
                'month' => ($this->session->getSection('app')->calendarMonth)? $this->session->getSection('app')->calendarMonth : $actualMonth,
                'schoolYear' => $this->session->getSection('app')->schoolYear
            );
        }else{
            $date = array(
                'year' =>  $this->session->getSection('app')->calendarYear ,
                'month' => $this->session->getSection('app')->calendarMonth ,
                'schoolYear' => $this->session->getSection('app')->schoolYear
            );
        }

        $this['filterForm']->setValues($date);
    }

    public function actionDefault($id = null){
        $this->redirect('actions');
    }

    public function actionFilter($type, $id = null){

        if(!$type){
            $this->error('404');
        }

        if($id == 10){
            if($type == Actions::ACTION)
                $this->redirect('actions');
            elseif($type == Actions::REPORT)
                $this->redirect('report');
        }

        $this->session->getSection('app')->calendarType = $type;
        $this->session->getSection('app')->calendarTroopId = $id;

        $this->filterId = $id;
        $this->dataCollection =
            $this->actions->filter(
                $id,
                $type,
                $this->session->getSection('app')->calendarYear,
                $this->session->getSection('app')->calendarMonth
            );

        $this->session->getSection('app')->postCount = $this->dataCollection->count();

        if($type == Actions::ACTION)
            $this->setView('actions');
        elseif($type == Actions::REPORT)
            $this->setView('report');

    }

    public function renderActions(){
        $this->template->actions = $this->dataCollection
            ->orderBy('date_from DESC')
            ->limit($this['paginator']->getPaginator()->itemsPerPage, $this['paginator']->getPaginator()->offset);
    }

    public function renderReport(){
        $this->template->reports = $this->dataCollection
            ->orderBy('date_from DESC')
            ->limit($this['paginator']->getPaginator()->itemsPerPage, $this['paginator']->getPaginator()->offset);
    }

    public function renderReportDetail($id){
        $this->template->report = $this->action;
    }

    public function actionActionDetail($id = NULL){
        $this->action =  $this->actions->get($id);
        if($id == null || !$this->action){
          $this->error('404');
        }
    }

    public function actionReportDetail($id = NULL){
        $this->action =  $this->actions->get($id);
        if($id == null || !$this->action){
            $this->error('404');
        }
    }

    public function renderActionDetail($id){
        $this->template->action = $this->action;
    }

    protected function createComponentGallery(){
        $gallery = $this->gallery;
        $gallery->isAdmin = false;
        $gallery->gallery = $this->action->getGallery();
        $gallery->action = $this->action;

        return $gallery;
    }

    protected function createComponentOrderForm(){
        $form = new Form();
        $form->addDatePicker('date');

        return $form;
    }

    protected function createComponentPaginator(){
        $vp = new \NasExt\Controls\VisualPaginator();
        $p = $vp->getPaginator();
        $p->itemsPerPage = self::POSTS_PER_PAGE;
        $p->itemCount = $this->session->getSection('app')->postCount;

        return $vp;
    }

    protected function createComponentFilterForm(){
        $form = new Form();

        $form->addSelect('year', 'Rok: ', $this->actions->getYears())
            ->setPrompt('Rok');

        $datetime = new DateTime();
        $year = (int)$datetime->format('Y') + 1;
        $years = array();
        for($i = 2005; $i < $year; $i++){
            $years[ $i . "-" . ($i + 1)] = $i . "-" . ($i + 1);
        }
        $years = array_reverse($years);
        $form->addSelect('schoolYear', 'Školní rok: ', $years)
            ->setPrompt('Školní rok');

        $form->addHidden('type');
        $form->addSelect('month', 'Měsíc: ',
            array(1 => 'Leden', 2 => "Únor", 3 => "Březen", 4 => "Duben", 5 => "Květen", 6 => "Červen",
                  7 => "Červenec", 8 => "Srpen", 9 => "Září", 10 => "Říjen", 11 => "Listopad", 12 => "Prosinec"
            )
        )
            ->setPrompt('Měsíc');
        $form->addSubmit('filter', 'Filtrovat')
            ->onClick[] = $this->filterForm;

        $form->addSubmit('filterExit', 'Zruš filtr')
            ->onClick[] = $this->exitFilter;

        return $form;
    }

    function filterForm(\Nette\Forms\Controls\SubmitButton $button){
        $values = $button->getForm()->values;
        $this->filter = $values;
        $this->session->getSection('app')->calendarYear = $values->year;
        $this->session->getSection('app')->calendarMonth = $values->month;
        $this->session->getSection('app')->schoolYear = $values->schoolYear;
        $this->session->getSection('app')->year = $values->year;

        $this['paginator']->getPaginator()->page = null;
        //dump( $this['paginator']->getPaginator()->getPage());

        $this->redirect('this', array('paginator-page' => null));
    }

    function exitFilter(){
        unset($this->session->getSection('app')->calendarYear);
        unset($this->session->getSection('app')->calendarMonth);
        unset($this->session->getSection('app')->schoolYear);
        unset($this->session->getSection('app')->year);

        $this->redirect('this');
    }

} 