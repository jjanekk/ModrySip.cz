<?php
namespace App;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{

	protected function beforeRender()
	{
        parent::beforeRender();
		$this->template->viewName = $this->view;
		$this->template->root = isset($_SERVER['SCRIPT_FILENAME']) ? realpath(dirname(dirname($_SERVER['SCRIPT_FILENAME']))) : NULL;

		$a = strrpos($this->name, ':');
		if ($a === FALSE) {
			$this->template->moduleName = '';
			$this->template->presenterName = $this->name;
		} else {
			$this->template->moduleName = substr($this->name, 0, $a + 1);
			$this->template->presenterName = substr($this->name, $a + 1);
		}

        $ip = $this->getHttpRequest()->getRemoteAddress();
        if($ip == "::1" || $ip == "127.0.0.1" || $ip == "localhost"){

        }else{
            $this->template->basePath = 'http://www.modrysip.cz';
        }
	}

}
