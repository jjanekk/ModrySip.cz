<?php
namespace App\FrontModule;

use Nette\Application\UI\Presenter;
use Orm\Repository\Actions as ACS;
use Orm\Repository\Pages as PS;
use Orm\Repository\Scout_troops as STS;


class PagePresenter extends FrontPresenter{

    /** @var  \Orm\Entity\Page */
    protected $page;


    function actionShow($id){
        $this->page = $this->pages->get($id);
        if(!$this->page && $id || $id === NULL){
            $this->error('404');
        }

        if(isset($this->page->template) && $this->page->template == 1){
            $this->setView('about');
        }

        if(isset($this->page->template) && $this->page->template == 2){
            $this->setView('aboutTroop');
        }
    }

    function renderShow($id){
        $this->template->page = $this->page;
    }

    function renderAbout($id){
        $this->template->page = $this->page;
    }

    function renderAboutTroop($id){
        $this->template->page = $this->page;
    }

}

