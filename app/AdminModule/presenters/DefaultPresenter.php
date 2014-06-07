<?php

namespace App\AdminModule;

use components\ActionGrid\Grid;
use Orm\Repository\Actions as ACS;

class DefaultPresenter extends AdminPresenter
{
    /** @var  \Orm\Repository\Actions @inject*/
    public $actions;

    /** @var  \Orm\Repository\Pages @inject*/
    public $pages;

    /** @var  \Orm\Repository\News @inject*/
    public $news;

    public function renderDefault(){
        $this->template->actions = $this->actions->getAll();
        $this->template->pages = $this->pages->getAll();
        $this->template->news = $this->news->getAll();
    }

}
