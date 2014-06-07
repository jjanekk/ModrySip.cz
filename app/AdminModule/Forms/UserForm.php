<?php
namespace AdminModule\Forms;

use Nette\Application\UI\Form;
use Nette\DateTime;
use Orm\Repository\Scout_troops as Sts;
use Orm\Repository\User_Functions;
use Orm\Repository\Users;

class UserForm extends Form{

    /** @var  Sts */
    protected $scout_troops;


    function __construct(User_Functions $ufs, Sts $sts){
        parent::__construct();
        $this->scout_troops = $sts;
        $this->build($ufs);
    }

    function build(User_Functions $ufs){
        $this->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer);

        $this->addGroup('');
        $this->addText('login', "Login")
            ->setAttribute('placeholder', 'Vyplňte prosím login.')
            ->setRequired('Položka Login je povinná. Prosím vyplňte ji.');

        $this->addText('nick', "Přezdívka");

        $this->addText('first_name', "Jméno");
        $this->addText('last_name', "Příjmení");
        $this->addText('phone', "Telefon");
        $this->addText('email', "Email")
            ->addCondition(Form::FILLED)
            ->addRule(Form::EMAIL, "Email není korektně napsán. Proveďte kontrolu překlepu.");
        $this->addSelect('role', 'Role', Users::$roles);
       /* $this->addSelect('user_function_id', 'Funkce', $ufs->fetchAll('id', 'name'))
            ->setPrompt('Bez funkce'); */
        $this->addSelect('scout_troop_id', 'Oddíl', $this->scout_troops->fetchAll('id', 'name'))
            ->setPrompt('Bez oddílu');
        $this->addCheckboxList('user_functions', 'Funkce', $ufs->fetchAll('id', 'name'));
        $this->addCheckbox('active', 'Aktivní uživatel')
            ->setValue(true);

        $this->addGroup('');
        $this->addUpload('photo', 'Fotografie');
        $this->addCheckbox('removePhoto', 'Odebrat fotku');

        $this->addGroup('');
        $this->addSubmit('save', 'Uložit');
    }

}