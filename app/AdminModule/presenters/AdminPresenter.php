<?php

namespace App\AdminModule;


use AdminModule\FormProcess\FormProcessFacade;
use components\breadcrumb\Breadcrumb;
use Nette\Security\Permission;
use Nette\Utils\Strings;
use Orm\Entity\User;
use Orm\Repository\Scout_troops;

abstract class AdminPresenter extends \App\BasePresenter{

    /** @var \AdminModule\FormProcess\FormProcessFacade @inject */
    public $processFormFacade;

    /** @var \Orm\Repository\Scout_troops  @inject */
    public $scout_troops;

    /** @var  \components\breadcrumb\Breadcrumb @inject */
    public $breadcrumb;

    /** @var  \Orm\Repository\Users @inject */
    public $users;

    /** @var User */
    protected $activeUserEntity;


    protected function startup()
    {
        parent::startup();
        $this->activeUserEntity = $this->users->get($this->getUser()->id);

        if($this->presenter->name != 'Admin:Sign' && !$this->user->isLoggedIn()){
            $this->redirect('Sign:in');
        }
    }

    public function getDataSource($filter, $order, \Nette\Utils\Paginator $paginator = NULL)
    {
        $selection = $this->prepareDataSource($filter, $order);
        if ($paginator) {
            $selection->limit($paginator->getItemsPerPage(), $paginator->getOffset());
        }
        return $selection;
    }

    public function getDataSourceSum($filter, $order)
    {
        return $this->prepareDataSource($filter, $order)->count('*');
    }

    public function getIUser(){
        return $this->users->get($this->user->id);
    }

    public function createComponentBreadcrumbs(){
        return $this->breadcrumb;
    }

} 