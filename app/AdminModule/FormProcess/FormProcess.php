<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 15.3.14
 * Time: 11:29
 */

namespace AdminModule\FormProcess;

use Nette,
    Kdyby;
use Orm\Facade\Mapper;

class FormProcess extends Nette\Object implements Kdyby\Events\Subscriber{

    /** @var  Mapper */
    protected $manager;

    /** @var  FormProcessFacade */
    protected $facade;

    function __construct ( \Orm\Facade\Mapper $manager, FormProcessFacade $facade)
    {
        $this->manager = $manager;
        $this->facade = $facade;
    }

    public function getSubscribedEvents()
    {
        return array('Nette\Application\Application::onStartup');
    }

    public function onStartup(Nette\Application\Application $app)
    {
        $this->manager->get('Files');
    }

}