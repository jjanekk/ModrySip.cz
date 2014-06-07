<?php
namespace Orm\Repository;

use Nette\Database\Table\ActiveRow;
use YetORM\Entity;
use Nette\Database\Context as NdbContext;

class Sliders extends Repository{
    protected $table = 'slider';
    protected $entity = 'Orm\Entity\slider';

    function getByTroopId($id){
        return $this->createCollection($this->getTable()->where('scout_troop_id', $id));
    }
} 