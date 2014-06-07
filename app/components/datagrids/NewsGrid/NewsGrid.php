<?php

namespace components\FileGrid;
    use components\BaseDataGrid;
    use Orm\Repository\Scout_troops;

class NewsGrid extends BaseDataGrid{

    function __construct(Scout_troops $sts)
    {
        parent::__construct();

        $this->setRowPrimaryKey('id');
        //$this->addColumn('text', '');
        $this->addColumn('name', 'Název')
            ->enableSort();
        $this->addColumn('user', 'Autor')
            ->enableSort();
        $this->addColumn('scout_troop_id', 'Oddíl')
            ->enableSort();
        $this->addColumn('active', 'Platná');
        $this->addColumn('date', 'Zveřejněno')
            ->enableSort();
        $this->addCellsTemplate(__DIR__ . '/@cells.latte');

        $this->setFilterFormFactory(function() use ($sts) {
            $form = new \Nette\Forms\Container;
            $form->addText('name');
            $form->addText('user');
            $form->addText('date');
            $form->addSelect('scout_troop_id', null, $sts->fetchAll('id', 'name'))
                ->setPrompt('-- Oddíl --');
            return $form;
        });
    }
}