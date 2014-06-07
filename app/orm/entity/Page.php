<?php
namespace Orm\Entity;

use YetORM;

/**
 * @property-read int $id
 * @property string $name
 * @property string|null $content
 * @property \Nette\Datetime $date
 * @property int|null $user_id
 * @property int|null $scout_troop_id
 * @property int|null $template
 */
class Page extends BaseEntity{

    /** @var Scout_troop[] */
    protected $addedTroops = array();

    /** @var Scout_troop[] */
    protected $removedTroops = array();



    function getUser(){
        if($en = $this->record->user)
            return new User($en);
        else
            return null;
    }

    function setUser(User $item){
        $this->record->user_id = $item->id;
        return $this;
    }

    function getScout_troop(){
        if($en = ($this->record->scout_troop))
            return new Scout_troop($en);
        else
            return null;
    }

    function setScout_troop(Scout_troop $item){
        $this->record->scout_troop_id = $item->id;
        return $this;
    }

    function addTroop($troop){
        $this->addedTroops[] = $troop;
        return $this;
    }

    function getAddedTroops(){
        $tmp = $this->addedTroops;
        return $tmp;
    }

    function removeTroop($troop){
        $this->removedTroops[] = $troop;
        return $this;
    }

    function getRemovedTroops()
    {
        $tmp = $this->removedTroops;
        return $tmp;
    }

    /** @return YetORM\EntityCollection */
    function getTroops($distinct = false)
    {
        if(!$distinct)
            return $this->getMany('\Orm\Entity\Scout_troop', 'page_has_troop', 'Scout_troop');
        else{
            $data = $this->getMany('\Orm\Entity\Scout_troop', 'page_has_troop', 'Scout_troop')->toArray();
            $result = array();
            foreach($data as $item){
                $same = false;
                foreach($result as $line){
                    if($line->name == $item->name) $same = true;
                }
                if(!$same) $result[] = $item;
            }

            return $result;
        }
    }

    /** @return array */
    function toArray()
    {
        $return = parent::toArray();

        $return['scout_troop'] = array();
        foreach ($this->getTroops() as $st) {
            $return['scout_troop'][] = $st->id;
        }

        return $return;
    }

}
