<?php
/**
 * Created by PhpStorm.
 * User: Tomáš
 * Date: 15.3.14
 * Time: 12:10
 */

namespace Orm\Facade;


use Nette\DI\Container;
use Nette;

class Mapper extends Nette\Object{

    /** @var  Nette\DI\Container */
    protected $container;

    protected $repositories;


    function __construct ( Nette\DI\Container $container )
    {
        $this->container = $container;
    }

    function get($name){
        return $this->container->getByType("\Orm\Repository\\" . $name);
    }


} 