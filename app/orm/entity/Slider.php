<?php

namespace Orm\Entity;
use YetORM;
use Nette\DateTime;

/**
 * @property-read int $id
 * @property int|null $file_id
 * @property int $scout_troop_id
 * @property string $name
 */

class Slider extends BaseEntity
{

    function setFile($file){
        $this->file_id = $file->id;
    }

    function getFile(){
        return new File($this->record->file);
    }

}