<?php
namespace App\FrontModule;

use App\BasePresenter;
use EventCalendar\Simple\EventCalendar;
use FrontModule\components\headers\Headers;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\DateTime;
use Nextras\Forms\Rendering\Bs3FormRenderer;
use Orm\Entity\Action;
use Orm\Entity\Data;
use Orm\Repository\Actions as ACS;
use Orm\Repository\Navigations;
use Orm\Repository\News;
use Orm\Repository\Pages as PS;
use Orm\Repository\Scout_troops as STS;
use Orm\Repository\Scout_troops;

abstract class FrontPresenter extends BasePresenter{

    /** @persistent  */
    public $troopUrl;

    /** @var  \Orm\Repository\Navigations @inject */
    public $menu;

    /** @var  \Orm\Repository\Settings @inject */
    public $settings;

    /** @var  Scout_troop */
    public $activeTroop;

    /** @persistent int   */
    public $actualType = 1;

    /** @var  \Orm\Repository\Sliders @inject */
    public $sliders;

    /** @var  PS */
    protected $pages;

    /** @var  STS */
    protected $scout_troops;

    /** @var  ACS */
    protected $actions;

    /** @var  Action */
    protected $action;

    /** @var  News */
    protected $news;

    /** @var  \FrontModule\components\headers\Headers @inject */
    public $headers;


    // ------------------------------------------------------------
    /** @var  Scout_troops */
    protected $collectionOfTroop;


    public function inject(News $news, ACS $actions,PS $pages,STS $scout_troops){
        $this->news = $news;
        $this->actions = $actions;
        $this->pages = $pages;
        $this->scout_troops = $scout_troops;
    }

    protected function startup()
    {
        parent::startup();
        $this->activeTroop = $this->scout_troops->getBySlug($this->troopUrl);
        $this->collectionOfTroop = $this->scout_troops->getAll();
    }

    protected function beforeRender()
    {
        parent::beforeRender();
        $this->template->pages = $this->pages->getAll();
        $this->template->news = $this->news->getAllActive($this->activeTroop->id);
        $this->template->scoutTroop = $this->collectionOfTroop;
        $this->template->scoutTroops = $this->scout_troops;
        $this->template->activeTroop = $this->activeTroop;
        $this->template->menu = $this->menu->getByTroop($this->activeTroop)->orderBy('level');
        $this->template->activeSite = $this->link('this');
        $this->template->sliders = $this->sliders->getByTroopId($this->activeTroop->id);
        $this->template->activeTroop = $this->activeTroop;
        $this->template->logos = $this->settings->getByTag('logo');
    }

    protected function createComponentUser(){
        $form = new Form();
        $form->setRenderer(new Bs3FormRenderer());
        $form->addSelect('user', 'Uživatel', array('parent' => 'Rodič', 'scout' => 'Skaut'))
            ->setPrompt('Nevybráno');

        return $form;
    }

    protected function createComponentHeaders(){
        $this->headers->troopID = $this->activeTroop->id;
        return $this->headers;
    }

    /** return bool */
    function isHome(){
        return $this->activeTroop->id == 10 || $this->activeTroop == null;
    }

    function isTroop(){
        return $this->activeTroop->id != 10 || $this->activeTroop != null;
    }

    function getTranslateDate($date){
        $date = new DateTime($date);
        $day = $date->format('l');
        switch($day)
        {
            case "Monday":    $day = "Pondělí";  break;
            case "Tuesday":   $day = "Úterý"; break;
            case "Wednesday": $day = "Středa";  break;
            case "Thursday":  $day = "Čtvrtek"; break;
            case "Friday":    $day = "Pátek";  break;
            case "Saturday":  $day = "Sobota";  break;
            case "Sunday":    $day = "Neděle";  break;
            default:          $day = "Neznámý"; break;
        }
        return $day;
    }



} 