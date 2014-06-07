<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 27.1.14
 * Time: 19:14
 */

namespace components\ActionGrid;
    use components\BaseDataGrid;
    use Orm\Repository\Users;

class UserGrid extends BaseDataGrid{

    function __construct(\Orm\Repository\User_Functions $ufs)
    {
        parent::__construct();
        $this->setRowPrimaryKey('id');
        $this->addColumn('name', 'Jméno')
            ->enableSort();
        $this->addColumn('phone', 'Telefon')
            ->enableSort();
        $this->addColumn('email', 'Email')
            ->enableSort();
        $this->addColumn('role', 'Role')
            ->enableSort();
        $this->addColumn('user_function_id', 'Fuknce')
            ->enableSort();
        $this->addColumn('active', 'Aktivní')
            ->enableSort();
        $this->addCellsTemplate(__DIR__ . '/@cells.latte');
        //$this->template->roles = Users::$roles;

        $this->setFilterFormFactory(function() use($ufs){
            $form = new \Nette\Forms\Container;
            $form->addText('nick');
            $form->addText('name');
            $form->addText('last_name');
            $form->addText('phone');
            $form->addText('email');
            $form->addSelect('role', null, Users::$roles)
                ->setPrompt('Vybrat rolli');
            $form->addSelect('user_function_id', null, $ufs->fetchAll('id', 'name'))
                ->setPrompt('Vybrat funkci');
            $form->addCheckbox('active', ' Aktivní');
            return $form;
        });
    }
}