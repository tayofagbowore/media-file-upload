<?php

class Loader {
    private $controller;
    private $action;
    private $url_values;
    private $has_error;

    const CONTROLLER = 'controller';
    const ACTION = 'action';

    public function __construct($url_values){
        $this->url_values = $url_values;
        $this->url_values[Loader::CONTROLLER] = (isset($this->url_values[Loader::CONTROLLER])) ? $this->url_values[Loader::CONTROLLER] : "";
        $this->url_values[Loader::ACTION] = (isset($this->url_values[Loader::ACTION])) ? $this->url_values[Loader::ACTION] : "";
        if ($this->url_values[Loader::CONTROLLER] == ""){
            $this->controller = Text::camelCase('home_' . Loader::CONTROLLER); //default
        }else{
            $controller_name = strtolower($this->url_values[Loader::CONTROLLER]);
            $this->controller = Text::camelCase($controller_name . '_' . Loader::CONTROLLER);
        }
        if ($this->url_values[Loader::ACTION] == ""){
            $this->action = 'index';//default
        }else{
            $this->action = Text::camelCase($this->url_values[Loader::ACTION], true);
        }
        $this->has_error =false;
    }

    public function hasError(){
        return $this->has_error;
    }

    public function createController(){
        //does the class exists?
        if (class_exists($this->controller)){
            $parents = class_parents($this->controller);
            //does the class extend the controller class?
            if (in_array("BaseController", $parents)){
                //does the class contain the requested method?
                if (method_exists($this->controller, $this->action)){
                    return new $this->controller($this->action,$this->url_values);
                }else{
                    $this->has_error =true;
                    return MessageHandler::error('Invalid action');
                }
            }else{
                $this->has_error =true;
                return MessageHandler::error('Invalid controller');
            }
        }else{
            $this->has_error =true;
            return MessageHandler::error('Controller not existing!');
        }
    }
}