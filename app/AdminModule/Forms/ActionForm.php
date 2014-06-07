<?php
namespace AdminModule\Forms;


use Nette\Application\UI\Form;
use Nette\DateTime;
use Orm\Repository\Scout_troops as Sts;

class ActionForm extends Form{

    /** @var  Sts */
    protected $scout_troops;

    function __construct(Sts $sts){
        parent::__construct();
        $this->scout_troops = $sts;

        $this->build();
    }

    function build(){
        $this->addGroup('');
        $this->addText('name', "Název")
            ->setRequired('Proím vyplňte název akce')
            ->setAttribute('placeholder', 'Stručný název akce');
        $this->addDatePicker('date_from', "Datum")
            ->setValue(new DateTime());
        $this->addDatePicker('date_to', "Datum ukončení");
        $this->addCheckboxList('scout_troop', 'Oddíly', $this->scout_troops->fetchAll('id', 'name'));

        $this->addGroup('');
        $this->addTextArea('action', "Info o akci");
        $this->addTextArea('report', "Reportáž z akce");
        $this->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer);

        $this->addSubmit('save', 'Uložit');
    }


}