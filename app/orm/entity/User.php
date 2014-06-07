<?php

namespace Orm\Entity;

use Orm\Repository\Users;
use YetORM;
use Nette\DateTime;
use Nette\Database\Table\ActiveRow as NActiveRow;

/**
 * @property-read int $id
 * @property string $nick
 * @property string $login
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $role
 * @property string $email
 * @property string $password
 * @property string|null $phone
 * @property \Nette\DateTIme|null $date_of_birth
 * @property boolean $active
 * @property int|null $file_id
 * @property int|null $scout_troop_id
 */

class User extends BaseEntity
{
    /** @var Scout_troop[] */
    protected $addedFunctions = array();

    /** @var Scout_troop[] */
    protected $removedFunctions = array();
    //------------------------------------


    public function getName($nick = true){
        if(!empty($this->record->first_name) && !empty($this->record->last_name)){
            return $this->record->first_name . ' ' . $this->record->last_name;
        }elseif(isset($this->record->nick) && $nick){
            return $this->record->nick;
        }else{
            return null;
        }
    }

    function getUserFunction(){
        return new User_function($this->record->user_function);
    }

    function hasPhoto(){
        return $this->file_id != null;
    }

    function getPhoto(){
        return new File($this->record->file);
    }

    function getRoleName(){
        return $this->role;
    }

    function addFunction($function){
        $this->addedFunctions[] = $function;
        return $this;
    }

    function getAddedFunctions(){
        $tmp = $this->addedFunctions;
        return $tmp;
    }

    function removeFunction($function){
        $this->removedFunctions[] = $function;
        return $this;
    }

    function getRemovedFunctions()
    {
        $tmp = $this->removedFunctions;
        return $tmp;
    }

    /** @return YetORM\EntityCollection */
    function getFunctions($distinct = false)
    {
        if(!$distinct)
            return $this->getMany('\Orm\Entity\User_function', 'user_has_functions', 'user_function');
        else{
            $data = $this->getMany('\Orm\Entity\User_function', 'user_has_functions', 'user_function')->toArray();
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

        $return['user_functions'] = array();
        foreach ($this->getFunctions() as $st) {
            $return['user_functions'][] = $st->id;
        }

        return $return;
    }

    

}
