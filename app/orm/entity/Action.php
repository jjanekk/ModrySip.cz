<?php

namespace Orm\Entity;

use components\Gallery\Gallery as Gall;
use YetORM;
use Nette\DateTime;
use Nette\Database\Table\ActiveRow as NActiveRow;

/**
 * @property-read int $id
 * @property string $name
 * @property string $name_report
 * @property int|null $user_id
 * @property \Nette\DateTIme|null $date_from
 * @property \Nette\DateTIme|null $date_to
 * @property int|null $fotogalery_id
 * @property string|null $action
 * @property string|null $report
 * @property id|null $image_id
 */
class Action extends BaseEntity
{
    /** @var Scout_troop[] */
    protected $addedTroops = array();

    /** @var Scout_troop[] */
    protected $removedTroops = array();

    //---------------------------------

    /** @return Image */
    function getImage()
    {
        if($en = $this->record->image)
            return new Image(en);
        else
            return null;
    }

    /**
     * @param  Image
     * @return Action
     */
    function setImage(Image $item)
    {
        $this->record->image_id = $item->id;
        return $this;
    }

    /** @return User */
    function getUser()
    {
        if($en = $this->record->user)
            return new User($en);
        else
            return null;
    }

    /**
     * @param  User
     * @return News
     */
    function setUser(User $item)
    {
        $this->record->user_id = $item->id;
        return $this;
    }

    function getGallery(){
        if($en = ($this->record->fotogalery))
            return new \Orm\Entity\Gallery($en);
        else
            return null;
    }

    function setGallery(\Orm\Entity\Gallery $gallery){
        $this->fotogalery_id = $gallery->id;
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
            return $this->getMany('\Orm\Entity\Scout_troop', 'action_has_troop', 'Scout_troop');
        else{
            $data = $this->getMany('\Orm\Entity\Scout_troop', 'action_has_troop', 'Scout_troop')->toArray();
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

    function hasGallery(){
        return $this->record->fotogalery_id != null ? true : false;
    }

    function hasGalleryWithImage(){
        if($this->hasGallery())
            return $this->getGallery()->hasImage();
        else
            false;
    }

    function isWill(){
        return $this->date_from > new DateTime();
    }

    function setTroop($id){
        $this->record->scout_troop_id = $id;
    }
}
