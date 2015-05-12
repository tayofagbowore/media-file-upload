<?php

/**
 * Class BaseController
 * ====================
 * It is the base controller in the Coex MVC framework
 */
abstract class BaseController {
    /**
     * @var array
     * ==========
     * It contains all the values from the url which include
     * the controller and action names, id and all url query variables
     */
    protected $url_values;

    /**
     * @var string
     * ===========
     * It is the name of the method to act
     */
    protected $action;

    /**
     * @var bool
     * =========
     * It check if authentication is needed
     */
    protected $auth_needed = true;

    public function __construct($action, $url_values){
        $this->action = $action;
        $this->url_values = $url_values;
//        var_dump($url_values);
        $this->initModel();
    }

    public function getAuthNeeded(){
        return $this->auth_needed;
    }

    protected function setAuthNeeded($auth_needed){
        $this->auth_needed = $auth_needed;
    }

    /**
     * executeAction
     * =============
     * It executes the target method specified in the action
     *
     * Note: for this method to work,
     *          all parameters to the target method must be passed along in the 'url_values'
     *          with the exception of optional parameters whose inclusion is also optional in the url_values
     */
    public function executeAction(){
        $method = new ReflectionMethod(get_class($this), $this->action);
        $params = $method->getParameters();
        $j = 0;
        for ($i = 0; $i < count($params); ++$i){
            if (isset($this->url_values[$params[$i]->getName()])){
                if ($this->url_values[$params[$i]->getName()] != ""){
                    $params[$i] = $this->url_values[$params[$i]->getName()];
                    $j++;
                    continue;
                }
            }

            if ($params[$i]->isDefaultValueAvailable()){
                $params[$i] = $params[$i]->getDefaultValue();
                $j++;
            }
        }

        if ($j != count($params)){
            return MessageHandler::error('Wrong number of parameters!');
        }else{
            return call_user_func_array(array($this, $this->action), $params);
        }
    }

    /**
     * output [to be upgraded]
     * ======
     * Just echoes stuff
     * @param $response
     */
    public function output($response){
        echo $response;
    }

    /**
     * authentication
     * ==============
     * It check if user has authentication to call a particular action
     *
     * @return bool
     */
    public function authentication(){
        $allow = true;

        if (get_class($this) == 'HomeController'){
            if (in_array($this->action, array('index', 'testMe', 'admin', 'client','adminmain'))){
                $this->setAuthNeeded(false);
            }
        }
        
        if (get_class($this) == 'ClientController'){
            if (in_array($this->action, array('index', 'help','signUp','login', 'clientNames', 'clientName', 'logout'))){
                $this->setAuthNeeded(false);
            }
        }
        
        if (get_class($this) == 'AdvertController'){
            if (in_array($this->action, array('getAllAdvert','initialize','update', 'getAdvertDetail', 'datasource', 'verify', 'reject', 'fileUpload', 'getPendingAdverts', 'getCountOfActiveAdverts', 'getCountOfPendingAdverts'))){
                $this->setAuthNeeded(false);
            }
        }

        if (get_class($this) == 'GtvController'){
            if (in_array($this->action, array('index'))){
                $this->setAuthNeeded(false);
            }
        }

        if ($this->getAuthNeeded()){
            if (CoexSessionHandler::isSetCredentials()){
                if (CoexSessionHandler::isSetCredentialItem(USER_ID)){
                    if ($this->url_values[ID] != CoexSessionHandler::getCredentialItem(USER_ID)){
                        $allow = false;
                    }
                }else{
                    $allow = false;
                }
            }else{
                $allow = false;
            }
        }

        return $allow;
    }

    /**
     * initModel
     * =========
     * It is used to initialise all models needed in the controller
     */
    abstract protected function initModel();
}