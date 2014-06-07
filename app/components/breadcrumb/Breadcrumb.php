<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 12.3.14
 * Time: 15:41
 */

namespace components\breadcrumb;


use Nette\Application\UI\Control;

class Breadcrumb extends Control{

    /** @var array */
    protected $args;


    function __construct(){}

    public function render(){
        $this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'template.latte');
        $this->template->args = $this->getData();
        $this->template->current = $this->presenter->link('this');
        $this->template->render();
    }

    function getData(){
        return $this->args;
    }

    public function  setData($data = array()){
        if(is_array($data))
            $this->args = $data;
        else{
            throw new \BadMethodCallException;
        }
    }

} 