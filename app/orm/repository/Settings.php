<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 19.3.14
 * Time: 19:03
 */

namespace Orm\Repository;


class Settings extends Repository{
    protected $table = 'data';
    protected $entity = 'Orm\Entity\Data';


    function getByTag($tag, $troopID = NULL){
        $result = $this->getTable()->where('tag', $tag);
        if($troopID != NULL){
            $result->where('scout_troop_id', $troopID);
        }

        return $this->createCollection($result);
    }

} 