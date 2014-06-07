<?php
namespace components\FileGrid;
    use components\BaseDataGrid;
    use Orm\Repository\Scout_troops;

class TroopGrid extends BaseDataGrid{

    function __construct(Scout_troops $sts)
    {
        parent::__construct();
        $this->setRowPrimaryKey('id');
        $this->addColumn('color', 'Barva');
        $this->addColumn('name', 'Název')
            ->enableSort();
        $this->addColumn('description', 'Popis')
            ->enableSort();
        $this->addColumn('showInList', 'V seznamu')
            ->enableSort();
        $this->addColumn('level', 'Pořadí')
            ->enableSort();
        $this->addCellsTemplate(__DIR__ . '/@cells.latte');

        $this->setFilterFormFactory(function() use ($sts) {
            $form = new \Nette\Forms\Container;
            $form->addText('name');
            $form->addText('description');
            $form->addSelect('showInList', null, array(1 => "Ano", 0 => "Ne"))
                ->setPrompt('Vše');
            return $form;
        });
    }
}