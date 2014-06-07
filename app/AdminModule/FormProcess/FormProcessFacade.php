<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 15.3.14
 * Time: 12:46
 */

namespace AdminModule\FormProcess;


use AdminModule\Forms\PageForm;

class FormProcessFacade {

    /** @var  PageProcess */
    protected $page;


    function __construct ( PageProcess $page )
    {
        $this->page = $page;
    }

    function savePage(PageForm $form){
        $this->page->save($form);
    }

} 