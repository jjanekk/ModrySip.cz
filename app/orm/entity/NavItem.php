<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 24.2.14
 * Time: 20:28
 */

namespace Orm\Entity;

/**
 * @property-read int $id
 * @property string $name
 * @property string|null $title
 * @property string|null $url
 * @property int|null $scout_troop_id
 * @property int|null $level
 */
class NavItem extends BaseEntity{

    function getTroop(){
        return new Scout_troop($this->record->scout_troop);
    }

} 