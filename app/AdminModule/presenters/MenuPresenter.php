<?php
namespace App\AdminModule;

use Nette\Application\UI\Form;
use Nextras\Forms\Rendering\Bs3FormRenderer;
use Orm\Entity\NavItem;

class MenuPresenter extends AdminPresenter{

    /** @persistent */
    public $id;

    /** @var  \Orm\Repository\Navigations @inject */
    public $menu;

    /** @var  \Orm\Repository\Pages @inject */
    public $pages;

    protected function startup()
    {
        parent::startup();
        $this->breadcrumb->setData(array('Menu:default' => 'Menu', 'Menu:troopNav' => 'Oddíl' , 'Menu:edit' => 'Editace'));
    }

    function renderDefault(){
        $this->template->troops = $this->scout_troops->getAll();
        $this->template->navs = $this->menu->getAll();
    }

    function renderTroopNav($id){
        $this->template->troop = $this->scout_troops->get($id);
        $this->template->nav = $this->menu->getByTroop($this->id)->orderBy('level');
    }

    function createComponentItem(){
        $form = new Form();
        $form->setRenderer(new Bs3FormRenderer(true));
        $form->addHidden('id');
        $form->elementPrototype->class = 'form-inline';
        $form->addText('name', null)
            ->setAttribute('placeholder', 'Název - povinný');
        $form->addText('title', null)
            ->setAttribute('placeholder', 'Titulek - nepovinný');
        $form->addText('link', null)
            ->setAttribute('placeholder', 'Odkaz');

        $data = $this->pages->fetchByTroop($this->id, 'id', 'name');

        $data[':Front:Calendar:report'] = '- Repotáže';
        $data[':Front:Calendar:actions'] = '- Akce';
        $data[':Front:Fotogalery:'] = '- Fotogalerie';
        $data[':Front:Default:'] = '- Titulní strana';
        $form->addSelect('url', null, $data)
            ->setPrompt('Vybrat stránku');
        $form->addSubmit('save', "Potvrdit");
        $form->onSuccess[] = $this->saveItem;
        return $form;
    }

    function saveItem(Form $form){
        $values = $form->values;
        $element = null;

        if(!$values->id){
            $element = new NavItem();
            $element->level = $this->menu->getLastOrder($this->id) + 1;
        }else{
            $element = $this->menu->get($values->id);

        }

        $element->name = $values->name;
        $element->title = $values->title;
        $element->url = (string)$values->url;
        $element->scout_troop_id = (int)$this->id;

        $this->menu->persist($element);
        $this->flashMessage('Uloženo.');

        if(!$this->isAjax())
            $this->redirect('this');

    }

    function handleEdit($itemId){
        $this['item']->setValues($this->menu->get($itemId)->toArray());
        $this->redrawControl('form');
    }

    function handleRemove($itemId){
        $el = $this->menu->get($itemId);
        $this->menu->delete($el);
        if($this->isAjax())
            $this->redrawControl('nav');
        else
            $this->redirect('this');
    }

    function handleUp($itemId){
        $this->menu->up($this->id, $itemId);
        if($this->isAjax())
            $this->redrawControl('nav');
        else
        $this->redirect('this');
    }

    function handleDown($itemId){
        $this->menu->down($this->id, $itemId);
        if($this->isAjax())
            $this->redrawControl('nav');
        else
            $this->redirect('this');
    }

} 