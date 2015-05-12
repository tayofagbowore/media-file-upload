<?php

class SqlClient {

    protected $pdo;
    protected $platform;

    protected $status;

    public static $error_message;

    const PLATFORM_MYSQL = 1;

    const STATUS_OK = 1;
    const STATUS_FAIL = 2;

    public function __construct($platform = SqlClient::PLATFORM_MYSQL){

        $this->platform = $platform;
        $this->init();
    }

    public function getStatus(){
        return $this->status;
    }

    public function getPlatform(){
        return $this->platform;
    }
    
    private function init(){
        $simple_xml = simplexml_load_file(WEB_CONFIG);
        //var_dump($simple_xml->data_source->add['dsn']);
        $this->status = SqlClient::STATUS_FAIL;
        try{
            switch($this->platform){
                case SqlClient::PLATFORM_MYSQL:
                    $connection_string = $simple_xml->data_source->add['dsn'];
                    $username = $simple_xml->data_source->add['username'];
                    $password = $simple_xml->data_source->add['password'];
                    $this->pdo = new PDO($connection_string, $username, $password);
                    $this->status = SqlClient::STATUS_OK;
                    break;
                default:
            }
        }catch (PDOException $e){
            SqlClient::$error_message = $e->getMessage();
        }
    }

    public function executeNonQuery($sql_statment, $params){
        if ($this->status == SqlClient::STATUS_OK and is_array($params)){
            $pds = $this->pdo->prepare($sql_statment);
//            echo $sql_statment.'<br>';
            $pds->execute($params);
            return $pds->rowCount();
        }
        return -1;
    }

    public function executeMultiNonQuery($sql_statment, $params_arr){
        //var_dump($params_arr);
        if ($this->status == SqlClient::STATUS_OK and is_array($params_arr)){
            $row_count = 0;
            $pds = $this->pdo->prepare($sql_statment);
            foreach($params_arr as $params){
                $pds->execute($params);
                $row_count += $pds->rowCount();
            }

            return $row_count;
        }
        return -1;
    }

    public function executeQuery($sql_statment, $params){

        if ($this->status == SqlClient::STATUS_OK && is_array($params)){
            $pds = $this->pdo->prepare($sql_statment);
            $pds->execute($params);
            return $pds;
        }
        return NULL;
    }

    public function fetchRow($pds, $is_assoc = true){
        $fetch_opt = ($is_assoc) ? PDO::FETCH_ASSOC : PDO::FETCH_NUM;
        if ($this->status == SqlClient::STATUS_OK and get_class($pds) == 'PDOStatement'){
            return $pds->fetch($fetch_opt);
        }
        return NULL;
    }

    public function fetchAll($pds, $is_assoc = true){
        $fetch_opt = ($is_assoc) ? PDO::FETCH_ASSOC : PDO::FETCH_NUM;
        if ($this->status == SqlClient::STATUS_OK and get_class($pds) == 'PDOStatement'){
            return $pds->fetchAll($fetch_opt);
        }
        return NULL;
    }

    public function getLastInsertedId(){
        return $this->pdo->lastInsertId();
    }

}

?>