<?php
namespace components\FileGrid;



use components\BaseDataGrid;

class FileGrid extends BaseDataGrid{

    function __construct()
    {
        parent::__construct();
        $this->setRowPrimaryKey('id');
        $this->addColumn('text', null);
        $this->addColumn('name', 'Název')
            ->enableSort();
        $this->addColumn('user', 'Autor')
            ->enableSort();
        $this->addColumn('date', 'Datum')
            ->enableSort();
        $this->addCellsTemplate(__DIR__ . '/@cells.latte');

        $this->setFilterFormFactory(function() {
            $form = new \Nette\Forms\Container;
            $form->addText('name')
                ->setAttribute('class', 'form-control');
            $form->addText('user')
                ->setAttribute('class', 'form-control');
            $form->addText('date')
                ->setAttribute('class', 'form-control');
            return $form;
        });
    }
}