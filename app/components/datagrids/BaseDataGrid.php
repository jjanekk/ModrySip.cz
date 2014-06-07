<?php
namespace components;


use Nextras\Datagrid\Datagrid;

class BaseDataGrid extends Datagrid{

    function __construct(){
        $this->addCellsTemplate(__DIR__ . DIRECTORY_SEPARATOR . "@baseTemplate.latte");
        $this->addCellsTemplate(__VENDOR__ . '/nextras/datagrid/bootstrap-style/@bootstrap3.datagrid.latte');
        $this->addCellsTemplate(__VENDOR__ . '/nextras/datagrid/bootstrap-style/@bootstrap3.extended-pagination.datagrid.latte');
    }

} 