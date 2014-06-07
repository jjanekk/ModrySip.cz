<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 12.3.14
 * Time: 0:07
 */

namespace App\AdminModule;

use Nette,
    App\Model;

class SignPresenter extends AdminPresenter{

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm()
    {
        $form = new Nette\Application\UI\Form;
        $form->addText('username', 'Email, nick:')
            ->setRequired('Prosím, vyplňte nick.')
            ->setAttribute('class', 'form-control');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosim, vyplňte heslo.')
            ->setAttribute('class', 'form-control');

        $form->addCheckbox('remember', 'Pamatuj si mě.');

        $form->addSubmit('send', ' Přihlásit se')
            ->setAttribute('class', 'btn btn-success');

        // call method signInFormSucceeded() on success
        $form->onSuccess[] = $this->signInFormSucceeded;
        return $form;
    }


    public function signInFormSucceeded($form)
    {
        $values = $form->getValues();

        if ($values->remember) {
            $this->getUser()->setExpiration('14 days', FALSE);
        } else {
            $this->getUser()->setExpiration('20 minutes', TRUE);
        }

        try {
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Default:');

        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }


    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('You have been signed out.');
        $this->redirect('in');
    }

}