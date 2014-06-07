<?php

namespace Orm\Entity;
use YetORM;
use Nette\DateTime;

/**
 * @property-read int $id
 * @property string $file_name
 * @property string $surfix
 * @property string|null $mime_type
 * @property string|null $description
 * @property int|null $fotogalery_id
 * @property boolean|null $isViewImage
 */

class File extends BaseEntity
{
    const SMALL_TITLE = 'd';

    function getName($small = false){
        // '/Galleries/user/'
        if(!$small)
            return $this->file_name . $this->surfix;
        else
            return $this->file_name . self::SMALL_TITLE . $this->surfix;
    }

}