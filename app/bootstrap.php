<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;
$configurator->setDebugMode(array('91.103.166.242', '127.0.0.1', '83.208.122.3'));  // debug mode MUST NOT be enabled on production server
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__ . '/../vendor/others')
	->addDirectory(__DIR__)
	->register();

define('__VENDOR__', __DIR__ . '/../vendor');

$configurator->addConfig(__DIR__ . '/config/config.neon');
if(is_file(__DIR__ . '/config/config.local.neon'))
    $configurator->addConfig(__DIR__ . '/config/config.local.neon');

use Nette\Forms\Container;
use Nextras\Forms\Controls;

Container::extensionMethod('addOptionList', function (Container $container, $name, $label = NULL, array $items = NULL) {
    return $container[$name] = new Controls\OptionList($label, $items);
});
Container::extensionMethod('addMultiOptionList', function (Container $container, $name, $label = NULL, array $items = NULL) {
    return $container[$name] = new Controls\MultiOptionList($label, $items);
});
Container::extensionMethod('addDatePicker', function (Container $container, $name, $label = NULL) {
    return $container[$name] = new Controls\DatePicker($label);
});
Container::extensionMethod('addDateTimePicker', function (Container $container, $name, $label = NULL) {
    return $container[$name] = new Controls\DateTimePicker($label);
});
Container::extensionMethod('addTypeahead', function(Container $container, $name, $label = NULL, $callback = NULL) {
    return $container[$name] = new Controls\Typeahead($label, $callback);
});


$container = $configurator->createContainer();



return $container;
