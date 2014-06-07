<?php
namespace App\FrontModule;


class NewsPresenter extends FrontPresenter{

    /** @var  \Orm\Entity\Page */
    protected $page;

    function actionShow($id){
        $this->page = $this->pages->get($id);
        if(!$this->page && $id){
            $this->error('404');
        }
    }

    function renderShow($id){
        $this->template->page = $this->page;
    }

} 