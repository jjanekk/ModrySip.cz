<?php
namespace Orm\Repository;

use Nette\Utils\Strings;

class Scout_troops extends Repository{
    protected $table = 'scout_troop';
    protected $entity = 'Orm\Entity\Scout_troop';


    function getBySlug($slug){
        $all = $this->getAll()->toArray();
        $result = null;
        foreach($all as $item){
            if(Strings::compare(Strings::webalize($item->name),$slug)){
                $result =  $item;
            }
        }
        if(!$result){
            $result = $this->get(10);
        }

        return $result;
    }

} 