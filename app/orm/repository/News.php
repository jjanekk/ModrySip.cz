<?php
namespace Orm\Repository;
use Nette\DateTime;
use YetORM\Entity;

class News extends Repository{
    protected $table = 'news';
    protected $entity = 'Orm\Entity\News';


    function getAllActive($troopID){
        return $this->createCollection($this->getTable()->where('date_to >= ? OR date_to IS NULL', new DateTime())->where(':news_has_troop.scout_troop_id = ? OR :news_has_troop.scout_troop_id = ?', $troopID, 10));
    }

    function getByTroop($id){
        $result = $this->getTable()->where('date_to >= ? OR date_to IS NULL', new DateTime());
        if($id && $id != 10){
            $result = $result->where('scout_troop_id = ? OR scout_troop_id = ?', $id, 10)->where('date_to >= ? OR date_to IS NULL', new DateTime());
        }
        return $this->createCollection(
            $result
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
                    'news_id' => $ac->id,
                    'scout_troop_id' => $a->id,
                );

                if ($this->getTable('news_has_troop')->where($data)->count() == 0 )
                    $this->getTable('news_has_troop')->insert($data);
            }

            $toDelete = array();
            foreach ($ac->getRemovedTroops() as $a) {
                $toDelete[] = $a->id;
            }

            if (count($toDelete)) {
                $this->getTable('news_has_troop')
                    ->where('news_id', $ac->id)
                    ->where('scout_troop_id', $toDelete)
                    ->delete();
            }
        }

        $this->commit();

        return $cnt;
    }

} 