<?php

namespace App\FrontModule;

use Nette\Application\UI\Presenter;
use Nette\Utils\Strings;
use Orm\Entity\Scout_troop;
use Orm\Repository\Actions as ACS;
use Orm\Repository\Pages as PS;
use Orm\Repository\Scout_troops as STS;

class TroopPresenter extends FrontPresenter
{
    public $id;

    /** @var  Scout_troop */
    protected $scout_troop;

    /** @persistent string */
    public $troop;

    protected function startup()
    {
        parent::startup();
        $id = null;
        foreach($this->scout_troops->getAll() as $troop){
            if(Strings::webalize($troop->name) ==  $this->troop){
                $id = $troop->id;
            }
        }
        if($id){
            $this->id = $id;
        }
    }

    public function actionShow($id = null){
      $this->scout_troop = $this->scout_troops->get($this->id);
      if(!$this->scout_troop && $this->id){
          $this->error('404');
      }
    }

    public function renderShow($id = null){
        $this->template->troop = $this->scout_troop;
}


}
