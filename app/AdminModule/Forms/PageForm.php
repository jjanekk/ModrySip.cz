<?php
namespace AdminModule\Forms;


use Nette\Application\UI\Form;
use Nette\DateTime;
use Orm\Repository\Pages;
use Orm\Repository\Scout_troops as Sts;

class PageForm extends Form{

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
            ->setAttribute('placeholder', 'Název stránky');
        $this->addSelect('template', 'Šablona', Pages::$templates)
            ->setPrompt('Bez šablony');
        $this->addCheckboxList('scout_troop', 'Oddíly', $this->scout_troops->fetchAll('id', 'name'));
        $this->addTextArea('content', "Obsah");
        $this->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer);

        $this->addSubmit('save', 'Uložit');
    }


}