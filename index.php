<?php
/**
 * @var $controller BaseController
 */

require_once('core/utility/FileLoader.php');

define('DIR_CONFIG', 'core/config');
define('DIR_UTILITY', 'core/utility');
define('DIR_DATA', 'core/data');
define('DIR_MODEL', 'core/model');
define('DIR_CONTROLLER', 'core/controller');

//FileLoader requires files in a specified directory
FileLoader::requireDir(DIR_CONFIG);
FileLoader::requireDir(DIR_DATA);
FileLoader::requireDir(DIR_UTILITY);
FileLoader::requireDir(DIR_MODEL);
FileLoader::requireDir(DIR_CONTROLLER);

//create the controller and execute the action
$loader = new Loader($_REQUEST);
$controller = $loader->CreateController();
if ($loader->hasError()){
    $response = $controller;
}else{
    if ($controller->authentication()){
        $response = $controller->executeAction();
    }else{
        $response = MessageHandler::accessDenied();
    }
}
echo $response;