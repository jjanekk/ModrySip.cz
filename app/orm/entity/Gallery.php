<?php
namespace Orm\Entity;

use YetORM\EntityCollection;

/**
 * @property-read int $id
 * @property int|null $action_id
 * @property int|null $scout_troop_id
 * @property string|null $dir
 * @property string|null $description
 */
class Gallery extends BaseEntity{

    function getName(){
        $record = $this->record->ref('action');
        if($record)
            return $record->name;
        else
            return 'Bez jména.';
    }

    function getImages(){
        return new EntityCollection($this->record->related('file'), 'File');
    }

    function getHomeImage(){
        $result = null;
        $record = $this->record->related('file')->where('isViewImage', 1);
        $c = $record->count();
        $r = $record->fetch();
        if($c > 0){
            $result = $r->file_name . 'd' . $r->surfix;
        }else{
            $record = $this->record->related('file');
            $c = $record->count();
            $r = $record->fetch();
            if($c > 0){
                $result = $r->file_name . 'd' . $r->surfix;
            }
        }

        return $result;
    }

    function getAction(){
        if(isset($this->record->related('action')->fetch()->name))
            return new Action($this->record->related('action')->fetch());
        else
            return NULL;
    }

    function hasImage(){
        return $this->record->related('file')->count();
        //return true;
    }


} 