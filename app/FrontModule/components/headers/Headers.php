<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 11.3.14
 * Time: 22:37
 */

namespace FrontModule\components\headers;


use Nette\Application\UI\Control;
use Orm\Repository\Users;

class Headers extends Control{

    /** @var  Users */
    protected $users;

    /** @var int */
    public $troopID;


    function __construct(\Orm\Repository\Users $users)
    {
        $this->users = $users;
    }

    public function render(){
        $this->template->setFile(__DIR__ .  DIRECTORY_SEPARATOR . 'headers.latte');
        $this->template->headers = $this->users->getAllHeaders();
        $this->template->basePath = 'http://www.modrysip.cz';
        $this->template->render();
    }

    public function renderTroop(){
        $this->template->setFile(__DIR__ .  DIRECTORY_SEPARATOR . 'troop.latte');
        $this->template->headers = $this->users->getAllTroopHeaders($this->troopID);
        $this->template->basePath = 'http://www.modrysip.cz';
        $this->template->render();
    }

} 