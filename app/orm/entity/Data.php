<?php

    namespace Orm\Entity;

    /**
     * @property-read int $id
     * @property string $name
     * @property string|null $tag
     * @property string|null $content
     * @property int|null $scout_troop_id
     * @property int|null $file_id
     */
    class Data extends BaseEntity{

        function getFile(){
            return new File($this->record->file);
        }

        function setFile(File $file){
            $this->file_id = $file->id;
        }

    }
