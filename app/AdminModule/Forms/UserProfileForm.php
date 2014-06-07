<?php
namespace AdminModule\Forms;

use Nette\Application\UI\Form;
use Nette\DateTime;
use Orm\Repository\Scout_troops as Sts;
use Orm\Repository\User_Functions;

class UserProfileForm extends Form{

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
        $this->addText('nick', "Nick")
            ->setAttribute('placeholder', 'Vyplňte prosím přezdívku.')
            ->setRequired('Položka Nick je povinná. Prosím vyplňte ji.');

        $this->addText('first_name', "Jméno");
        $this->addText('last_name', "Příjmení");
        $this->addText('phone', "Telefon");
        $this->addText('email', "Email")
            ->addCondition(Form::FILLED)
            ->addRule(Form::EMAIL, "Email není korektně napsán. Proveďte kontrolu překlepu.");

        $this->addGroup('');
        $this->addPassword('password', 'Heslo:')
            ->addCondition(Form::FILLED)
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaky', 3);
        $this->addPassword('passwordVerify', 'Heslo pro kontrolu:')
            ->addConditionOn($this['password'], Form::FILLED, true)
                ->setRequired('Zadejte prosím heslo ještě jednou pro kontrolu')
            ->addRule(Form::EQUAL, 'Hesla se neshodují', $this['password']);

        $this->addGroup('');
        $this->addUpload('photo', 'Fotografie');

        $this->addGroup('');
        $this->addSubmit('save', 'Uložit');
    }

}