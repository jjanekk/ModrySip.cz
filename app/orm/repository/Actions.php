<?php
namespace Orm\Repository;

use Nette\DateTime;
use Orm\Entity\Action;
use YetORM\Entity;

class Actions extends Repository{
    protected $table = 'action';
    protected $entity = 'Orm\Entity\Action';

    const ACTION = 'action', REPORT = 'report', MAX_ACTION_DAYS = 20;


    protected function getSelectionForReports($text = false){
        //@todo vycistit databazi od ---
        if($text)
            return $this->getSelection()->where('report IS NOT NULL AND LENGTH(report) > 15');
        else
            return $this->getSelection();
    }

    function persist(Entity $ac)
    {
        $this->begin();

        $cnt = parent::persist($ac);

        // persist tags
        if (count($ac->getAddedTroops()) || count($ac->getRemovedTroops())) {

            foreach ($ac->getAddedTroops() as $a) {
                $data = array(
                    'action_id' => $ac->id,
                    'scout_troop_id' => $a->id,
                );

                if ($this->getTable('action_has_troop')->where($data)->count() == 0 )
                    $this->getTable('action_has_troop')->insert($data);
            }

            $toDelete = array();
            foreach ($ac->getRemovedTroops() as $a) {
                $toDelete[] = $a->id;
            }

            if (count($toDelete)) {
                $this->getTable('action_has_troop')
                    ->where('action_id', $ac->id)
                    ->where('scout_troop_id', $toDelete)
                    ->delete();
            }
        }

        $this->commit();

        return $cnt;
    }

    function getReports($troopId = null, $date = null, $text = false){
        //@todo prace s datumem

        $result = $this->getSelectionForReports($text);
        if($troopId && $troopId != 10){
            $result = $result->where(':action_has_troop.scout_troop_id = ? OR :action_has_troop.scout_troop_id = ?', $troopId, 10);
        }

        if($date){
            $result = $result->where('date_from <= ? OR date_to <= ?', $date, $date);
        }else{
            $result = $result->where('date_from <= ?', new DateTime());
        }

        return $this->createCollectionFromSection(
            $result
        );
    }

    function getActual($troopId = null){
        $date = new DateTime();
        $date->add(new \DateInterval('P' . self::MAX_ACTION_DAYS . 'D'));
        $result = $this->getTable()->where('(date_from >= ? && date_from <= ?) || (date_to >= ? && date_to <= ?)', new DateTime(), $date, new DateTime(), $date);
        if($troopId && $troopId != 10){
            $result = $result->where(':action_has_troop.scout_troop_id = ? OR :action_has_troop.scout_troop_id = ?', $troopId, 10);
        }
        if($troopId != 10)
            $result->order('date_from DESC');
        else
            $result->order('date_from ASC');
        return $this->createCollection($result);
    }

    function getByTroop($id, $type = NULL){
        $result = $this->getTable();
        if($type == self::REPORT){
            $result = $this->getSelectionForReports();
        }

        return $this->createCollection(
            $result->where(':action_has_troop.scout_troop_id = ?', $id)
        );
    }

    function getYears(){
        return $this->getTable()->select('DISTINCT YEAR(date_from) AS year')->order('year DESC')->fetchPairs('year', 'year');
    }

    public function filter($id, $type, $year = null, $month = NULL, $schoolYear = NULL){
        $result =  $this->getTable();

        if($type == self::REPORT){
            $result = $this->getSelectionForReports();
        }elseif($type != self::ACTION){
            throw new \BadFunctionCallException('Parameter type is wrong. REPORT|ACTION is enabled.');
        }

        if($id != 10){
            $result = $result->where(':action_has_troop.scout_troop_id = ? || :action_has_troop.scout_troop_id = ?', $id, 10);
        }

        if($year){
            $result = $result->where('YEAR(date_from) = ? OR YEAR(date_to) = ?', $year, $year);
        }

        if($month){
            $result->where('MONTH(date_from) = ? OR MONTH(date_to) = ?', $month, $month);
        }

        if($type == self::REPORT){
            $result->where("date_from <= ?", new DateTime());
        }

        //dump($schoolYear);

        if($schoolYear){
            $result = $this->getBySchoolYear($schoolYear, $result);
        }
        //exit;

        return $this->createCollection($result)->orderBy('date_from DESC');
    }

    function getBySchoolYear($schoolYear, $result = null){
        if(!$result){
            $result = $this->getTable();
        }

        $years = explode('-', $schoolYear);
        $from = new DateTime($years[0] . '-9-1');
        $to =   new DateTime($years[1] . '-8-31');

        $result->where('(date_from >= ? AND date_from <= ?) || (date_to >= ? AND date_to <= ?)', $from, $to, $from, $to);
        return $result;
    }


    function getAllWithGallery($troop_id){
        $result = ($this->getTable()->where('fotogalery_id not NULL'));
        if($troop_id){
            $result->where(':action_has_troop.id = ?', $troop_id);
        }

        return $this->createCollection($result);
    }

} 