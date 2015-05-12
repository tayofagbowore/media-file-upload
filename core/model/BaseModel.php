<?php

abstract class BaseModel {

    /*Used for checking the status of the database response*/
    protected $db_status;

    /**
     * Constructor
     *
     * @param null $id
     */
    abstract public function __construct($id = NULL);

    /**
     * It is used for filling in data from an array.
     *
     * @param $array
     */
    abstract public function fillInData($array);

    /**
     * It is used for adding a new record to the database.
     *
     * @param $array
     * @return int
     *  A status code.
     */
    public function add($array){

        $params = $this->parameterize($array);
        $sql_statement_class = get_class($this).SQL_STATEMENT;
        if (class_exists($sql_statement_class) and defined($sql_statement_class.'::ADD')){
            $sql_client = new SqlClient();

            if ($sql_client->getStatus()){
                $sql_client->executeNonQuery($sql_statement_class::ADD, $params);
                return $sql_client->getLastInsertedId();
            }
        }
        return -1;
    }

    public function addAll($array){
        $params = $this->parameterizeAll($array);
        //var_dump($params);
        $sql_statement_class = get_class($this).SQL_STATEMENT;
        if (class_exists($sql_statement_class) and defined($sql_statement_class.'::ADD')){
            $sql_client = new SqlClient();
            if ($sql_client->getStatus()){

                return $sql_client->executeMultiNonQuery($sql_statement_class::ADD, $params);
            }
        }
        return -1;
    }

    /**
     * It is used for update an existing record to the database.
     *
     * @param $array
     * @return int
     *  A status code.
     */
    public function update($array){
        $params = $this->parameterize($array);
//        var_dump($params);
        $sql_statement_class = get_class($this).SQL_STATEMENT;
        if (class_exists($sql_statement_class) and defined($sql_statement_class.'::UPDATE')){
            $sql_client = new SqlClient();

            if ($sql_client->getStatus()){
//                echo $sql_statement_class::UPDATE;
                return $sql_client->executeNonQuery($sql_statement_class::UPDATE, $params);
            }
        }
        return -1;
    }

    public function updateAll($array){
        $params = $this->parameterizeAll($array);
        $sql_statement_class = get_class($this).SQL_STATEMENT;
        if (class_exists($sql_statement_class) and defined($sql_statement_class.'::UPDATE')){
            $sql_client = new SqlClient();
            if ($sql_client->getStatus()){
                return $sql_client->executeMultiNonQuery($sql_statement_class::UPDATE, $params);
            }
        }
        return -1;
    }
    

    public function delete($array){
        $params = $this->parameterize($array);
        $sql_statement_class = get_class($this).SQL_STATEMENT;
        if (class_exists($sql_statement_class) and defined($sql_statement_class.'::DELETE')){
            $sql_client = new SqlClient();
            if ($sql_client->getStatus()){
                return $sql_client->executeNonQuery($sql_statement_class::DELETE, $params);
            }
        }
        return -1;
    }

    /**
     * It is used to represent the object in form as an array
     *
     * @return array
     *  An array containing information
     */
    abstract public function getAsArray();

    public function fetchData($array){
        $params = $this->parameterize($array);
        $sql_statement_class = get_class($this).SQL_STATEMENT;
        if (class_exists($sql_statement_class) and defined($sql_statement_class.'::GET')){
            $sql_client = new SqlClient();
            if ($sql_client->getStatus()){
                $pds = $sql_client->executeQuery($sql_statement_class::GET, $params);
                return $sql_client->fetchRow($pds);
            }
        }
        return NULL;
    }

    public function fetchAllData($array){
        //var_dump($array);
        $params = $this->parameterize($array);
        $sql_statement_class = get_class($this).SQL_STATEMENT;
        //var_dump($sql_statement_class);

        if (class_exists($sql_statement_class) and defined($sql_statement_class.'::GET')){
            $sql_client = new SqlClient();
            if ($sql_client->getStatus()){
                //echo $sql_statement_class::GET;
                $pds = $sql_client->executeQuery($sql_statement_class::GET, $params);
                return $sql_client->fetchAll($pds);
            }
        }
        return NULL;
    }

    public function executeQuery($stmt, $data, $fetch_row = false){
        $params = $this->parameterize($data);
        $sql_client = new SqlClient();
        if ($sql_client->getStatus()){
            $pds = $sql_client->executeQuery($stmt, $params);
            return ($fetch_row) ? $sql_client->fetchRow($pds) : $sql_client->fetchAll($pds);
        }
        return NULL;
    }

    public function executeNonQuery($stmt, $data){
        $params = $this->parameterize($data);
        $sql_client = new SqlClient();
        if ($sql_client->getStatus()){
            return $sql_client->executeNonQuery($stmt, $params);
        }
        return -1;
    }

    /**
     * It gets the status of the previous database request
     *
     * @return mixed
     */
    public function getDbStatus(){

        return $this->db_status;
    }

    protected function parameterize($array){
        $params = array();
        $keys = array_keys($array);
        foreach($keys as $key){
            $params[':'.$key] = $array[$key];
        }
        return $params;
    }

    protected function parameterizeAll($array){
        $params = array();

        foreach ($array as $item){
            $params[] = $this->parameterize($item);
        }
        return $params;
    }
}