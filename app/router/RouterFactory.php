<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;
use Orm\Repository\Scout_troops;


/**
 * Router factory.
 */
class RouterFactory
{
    /** @var  Scout_troops */
    protected $scout_troops;

    function __construct(\Orm\Repository\Scout_troops $scout_troops)
    {
        $this->scout_troops = $scout_troops;
    }


    /**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();

        $router[] = new Route('index.php', 'Front:Default:default', Route::ONE_WAY);

        $router[] = $adminRouter = new RouteList('Admin');
        $adminRouter[] = new Route('admin/<presenter>/<action>', 'Default:default');

        $router[] = $frontRouter = new RouteList('Front');

        $string = '';
        foreach($this->scout_troops->getAll()  as $troop){
            $string .= (Nette\Utils\Strings::webalize($troop->name)) . '|';
        }
        $string = rtrim($string, "|");


        /*
            $frontRouter[] = new Route("//<troopUrl $string>.modrysip.cz/<presenter>/<action>[/<id>]", array(
                'presenter' => 'Default',
                'action' => 'default'
            ));

            $frontRouter[] = new Route("//www.modrysip.cz/<presenter>/<action>[/<id>]", array(
                'presenter' => 'Default',
                'action' => 'default'
            ));
        }
        */

        $frontRouter[] = new Route("<presenter>/<action>[/<id>]", array(
            'presenter' => 'Default',
            'action' => 'default'
        ));

        return $router;
	}

}
