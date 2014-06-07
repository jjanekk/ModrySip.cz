<?php
namespace Orm\Repository;

use Orm\Entity\Gallery;

class Galleries extends Repository{
    protected $table = 'fotogalery';
    protected $entity = 'Orm\Entity\Gallery';


    function getByTroop($id){
        $result = $this->getTable();
        if($id && $id != 10){
            $result = $result->where('scout_troop_id = ?', $id);
        }
        return $this->createCollection(
            $result
        );
    }

    function getByName($name){
        if($this->getTable()->where('dir', $name)->fetch())
            return new Gallery($this->getTable()->where('dir', $name)->fetch());
        else
            null;
    }

} 