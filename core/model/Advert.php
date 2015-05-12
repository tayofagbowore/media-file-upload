<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Advert
 *
 * @author user
 */
class Advert extends BaseModel {

    private $advert_id;
    private $client_id;
    private $advert_type_id;
    private $advert_span;
    private $advert_name;
    private $advert_status_id;
    private $start_date;
    private $advert_url;
    private $thumb_nail;
    private $created_date;
    private $modified_date;

    public function __construct($id = NULL) {
        
    }

    public function getAdvert_id() {
        return $this->advert_id;
    }

    public function setAdvert_id($advert_id) {
        $this->advert_id = $advert_id;
    }

    public function getClient_id() {
        return $this->client_id;
    }

    public function setClient_id($client_id) {
        $this->client_id = $client_id;
    }

    public function getAdvert_type_id() {
        return $this->advert_type_id;
    }

    public function setAdvert_type_id($advert_type_id) {
        $this->advert_type_id = $advert_type_id;
    }

    public function getAdvert_span() {
        return $this->advert_span;
    }

    public function setAdvert_span($advert_span) {
        $this->advert_span = $advert_span;
    }

    public function getAdvert_status_id() {
        return $this->advert_status_id;
    }

    public function setAdvert_status_id($advert_status_id) {
        $this->advert_status_id = $advert_status_id;
    }

    public function getExpiration_date() {
        return $this->start_date;
    }
    
    public function getAdvert_name() {
        return $this->advert_name;
    }

    public function setAdvert_name($advert_name) {
        $this->advert_name = $advert_name;
    }

    public function setExpiration_date($start_date) {
        $this->start_date = $start_date;
    }

    public function getCreated_date() {
        return $this->created_date;
    }

    public function setCreated_date($created_date) {
        $this->created_date = $created_date;
    }

    public function getModified_date() {
        return $this->modified_date;
    }

    public function setModified_date($modified_date) {
        $this->modified_date = $modified_date;
    }
    
    public function getStart_date() {
        return $this->start_date;
    }

    public function setStart_date($start_date) {
        $this->start_date = $start_date;
    }

    public function getAdvert_url() {
        return $this->advert_url;
    }

    public function setAdvert_url($advert_url) {
        $this->advert_url = $advert_url;
    }

    public function getThumb_nail() {
        return $this->thumb_nail;
    }

    public function setThumb_nail($thumb_nail) {
        $this->thumb_nail = $thumb_nail;
    }

    
    public function fillInData($array) {
        $this->advert_id = $array(AdvertTable::advert_id);
        $this->client_id = $array(AdvertTable::client_id);
        $this->advert_type_id = $array(AdvertTable::advert_type_id);
        $this->advert_span = $array(AdvertTable::advert_span);
        $this->advert_name = $array(AdvertTable::advert_name);
        $this->advert_url = $array(AdvertTable::advert_url);
        $this->thumb_nail = $array(AdvertTable::thumb_nail);
        $this->start_date = $array(AdvertTable::start_date);
        $this->advert_status_id = $array(AdvertTable::advert_status_id);
        $this->create_date = $array(AdvertTable::create_date);
        $this->modified_date = $array(AdvertTable::modified_date);
    }

    public function getAsArray() {
        $array = array();
        $array[AdvertTable::advert_id] = $this->getAdvert_id();
        $array[AdvertTable::client_id] = $this->getClient_id();
        $array[AdvertTable::advert_type_id] = $this->getAdvert_type_id();
        $array[AdvertTable::advert_span] = $this->getAdvert_span();
        $array[AdvertTable::advert_status_id] = $this->getAdvert_status_id();
        $array[AdvertTable::advert_name] = $this->getAdvert_name();
        $array[AdvertTable::advert_url] = $this->getAdvert_url();
        $array[AdvertTable::thumb_nail] = $this->getThumb_nail();
        $array[AdvertTable::start_date] = $this->getStart_date();
        $array[AdvertTable::create_date] = $this->getCreated_date();
        $array[AdvertTable::modified_date] = $this->getModified_date();

        return $array;
    }

    public function isAdvertExisting($advert_name){
        $params = array( AdvertTable::advert_name => $advert_name);
        $result = $this->executeQuery(AdvertSqlStatement::IS_ADVERT_EXISTING, $params, true);
//        var_dump($result);
        return ($result != false );
    }
    
    public function addAdvert($array) {
        $advert_id = $this->add(array(
            AdvertTable::client_id => $array[AdvertTable::client_id],
            AdvertTable::advert_type_id => $array[AdvertTable::advert_type_id],
            AdvertTable::advert_name => $array[AdvertTable::advert_name],
            AdvertTable::advert_span => $array[AdvertTable::advert_span],
            AdvertTable::start_date => $array[AdvertTable::start_date],
            AdvertTable::thumb_nail => $array[AdvertTable::thumb_nail],
            AdvertTable::advert_url => $array[AdvertTable::advert_url]
        ));

        return $advert_id;
    }

}

?>
