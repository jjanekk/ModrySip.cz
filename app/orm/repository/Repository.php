<?php
namespace Orm\Repository;


use Nette\DateTime;

abstract class Repository extends \YetORM\Repository{

    function get($id){
        if($row = $this->getTable()->get($id)){
            return new $this->entity($row);
        }
        return null;
    }

    function getAll($condition = null, $parameters = array()){
        if(!$condition)
            return $this->createCollection($this->getTable());
        else
            return $this->createCollection($this->getTable()->where($condition, $parameters));
    }

    function getSelection(){
        return $this->getTable();
    }

    function createCollectionFromSection($section){
        if($section != null)
            return $this->createCollection($section);
        else
            return null;
    }

    function fetchAll($index, $key){
        return $this->getTable()->fetchPairs($index, $key);
    }

} 