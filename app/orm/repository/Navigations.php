<?php
namespace Orm\Repository;

use Nette\Database\SqlLiteral;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Context as NdbContext;
use YetORM\Entity;

class Navigations extends Repository{
    protected $table = 'nav';
    protected $entity = 'Orm\Entity\NavItem';


    function getByTroop($id){
        $id = $this->getId($id);
        return $this->createCollection($this->getTable()->where('scout_troop_id', $id));
    }

    function getLastOrder($id){
        $id = $this->getId($id);
        return $this->getTable()->where('scout_troop_id', $id)->max('level');
    }

    protected function getId($item){
        $id = null;
        if(is_object($item) && $item instanceof Entity){
            $id = $item->id;
        }else{
            $id = $item;
        }
        return $id;
    }

    function up($id, $troopId){
        $order = $this->get($troopId)->level;
        $uId = $this->getTable()->where('level = ?', $order - 1)->where('scout_troop_id', $id)->fetch()->id;

        $this->getTable()->where('id', $troopId)->update(array('level' => new SqlLiteral('level - 1')));
        $this->getTable()->where('id', $uId)->update(array('level' => new SqlLiteral('level + 1')));

    }

    function down($id, $troopId){
        $order = $this->get($troopId)->level;
        $uId = $this->getTable()->where('level = ?', $order + 1)->where('scout_troop_id', $id)->fetch()->id;

        $this->getTable()->where('id', $troopId)->update(array('level' => new SqlLiteral('level + 1')));
        $this->getTable()->where('id', $uId)->update(array('level' => new SqlLiteral('level - 1')));
    }

    function delete(Entity $entity)
    {
        $row = parent::delete($entity);
        $this->getTable()->where('level > ?', $entity->level )->update( array('level' => new SqlLiteral('level - 1')) );
        return $row;
    }


}