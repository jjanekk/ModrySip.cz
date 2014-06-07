<?php
namespace AdminModule\Forms;
use Nette\Application\UI\Form;

class TroopForm extends Form{


    function build(){
        $this->addText('color', "Barva oddílu")
            ->setType('color');
        $this->addText('name', "Název")
            ->setRequired('Pole název je povinné, prosím vyplňte ho.')
            ->setAttribute('placeholder', 'Název oddílu');
        $this->addCheckbox('showInList', "Zobrazit v menu");
        $this->addText('level', "Pořadí pro zobrazení")
            ->setType('number')
            ->setAttribute('min', 0);

        $this->addTextArea('description', "Popis");
        $this->addTextArea('home_page_description', "Text uvodní stránky");
        $this->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer);

        $this->addSubmit('save', 'Uložit');
    }

}