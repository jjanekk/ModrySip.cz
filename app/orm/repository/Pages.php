<?php
namespace Orm\Repository;

use YetORM\Entity;

class Pages extends Repository{
    protected $table = 'page';
    protected $entity = 'Orm\Entity\Page';
    public static $templates = array(1 => 'Kontakty - vše', 2 => 'Kontakty - oddíl');


    function fetchByTroop($id, $index, $key){
        return $this->getTable()->where(':page_has_troop.scout_troop_id = ?', $id)->fetchPairs($index, $key);
    }

    function getByTroop($id, $type = NULL){
        $result = $this->getTable();
        if($type == self::REPORT){
            $result = $this->getSelectionForReports();
        }

        return $this->createCollection(
            $result->where(':page_has_troop.scout_troop_id = ?', $id)
        );
    }

    function persist(Entity $ac)
    {
        $this->begin();

        $cnt = parent::persist($ac);

        // persist tags
        if (count($ac->getAddedTroops()) || count($ac->getRemovedTroops())) {

            foreach ($ac->getAddedTroops() as $a) {
                $data = array(
                    'page_id' => $ac->id,
                    'scout_troop_id' => $a->id,
                );

                if ($this->getTable('page_has_troop')->where($data)->count() == 0 )
                    $this->getTable('page_has_troop')->insert($data);
            }

            $toDelete = array();
            foreach ($ac->getRemovedTroops() as $a) {
                $toDelete[] = $a->id;
            }

            if (count($toDelete)) {
                $this->getTable('page_has_troop')
                    ->where('page_id', $ac->id)
                    ->where('scout_troop_id', $toDelete)
                    ->delete();
            }
        }

        $this->commit();

        return $cnt;
    }
} 