<?php

namespace AdminModule\FormProcess;


use AdminModule\Forms\PageForm;
use Orm\Repository\Pages as PS;
use Orm\Repository\Scout_troops as STS;

class PageProcess {

    /** @var Scout_troops  */
    protected $troop;

    /** @var  Pages */
    protected $pages;


    function __construct ( PS $pages,STS $troop )
    {
        $this->pages = $pages;
        $this->troop = $troop;
    }

    function save(PageForm $form){

    }

} 