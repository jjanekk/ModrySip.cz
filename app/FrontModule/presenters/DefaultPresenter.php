<?php

namespace App\FrontModule;

use Nette\DateTime;
use Orm\Repository\Actions as ACS;
use Orm\Repository\Pages as PS;
use Orm\Repository\Scout_troops as STS;

class DefaultPresenter extends FrontPresenter
{

    function renderDefault(){
        // array_reverse
        $actions =  ( $this->actions->getActual($this->activeTroop->id)->limit(3)->toArray() );
        if($this->activeTroop->id == 10)
            $this->template->actions = $actions;
        else
            $this->template->actions = array_reverse($actions);

        $this->template->reports = $this->actions->getReports($this->activeTroop->id, new DateTime(), TRUE)->orderBy('date_from DESC')->limit(3)->toArray() ;
    }

}
