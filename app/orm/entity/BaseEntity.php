<?php
namespace Orm\Entity;

use Orm\collection\OrmEntityCollection;
use YetORM\Entity;
use YetORM\EntityCollection;

class BaseEntity extends Entity {

    /**
     * @param  string
     * @param  string
     * @param  string
     * @param  string
     * @return EntityCollection
     */
    protected function getMany($entity, $relTable, $entityTable, $throughColumn = NULL)
    {
        return new OrmEntityCollection($this->record->related($relTable), $entity, $entityTable, $throughColumn);
    }

    /**
     * Looks for all public get* methods and @property[-read] annotations
     * and returns associative array with corresponding values
     *
     * @return array
     */

    /** @return array */
    function toArray()
    {
        $ref = static::getReflection();
        $values = array();

        foreach ($ref->getEntityProperties() as $name => $property) {
            if ($property instanceof \YetORM\Reflection\MethodProperty) {
                $value = $this->{'get' . $name}();

            } else {
                $value = $this->$name;
            }

            if (!($value instanceof \YetORM\EntityCollection || $value instanceof \YetORM\Entity)) {
                $values[$name] = $value;
            }
        }

        return $values;
    }

} 