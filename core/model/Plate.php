<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Plate
 *
 * @author user
 */
class Plate extends BaseModel {

    private $plate_id;
    private $advert_id;
    private $plate_url;
    private $plate_status_id;
    private $create_date;
    private $modified_date;

    public function __construct($id = NULL) {
        
    }

    public function getPlate_id() {
        return $this->plate_id;
    }

    public function setPlate_id($plate_id) {
        $this->plate_id = $plate_id;
    }

    public function getAdvert_id() {
        return $this->advert_id;
    }

    public function setAdvert_id($advert_id) {
        $this->advert_id = $advert_id;
    }

    public function getPlate_url() {
        return $this->plate_url;
    }

    public function setPlate_url($plate_url) {
        $this->plate_url = $plate_url;
    }

    public function getCreate_date() {
        return $this->create_date;
    }

    public function setCreate_date($create_date) {
        $this->create_date = $create_date;
    }

    public function getModified_date() {
        return $this->modified_date;
    }

    public function setModified_date($modified_date) {
        $this->modified_date = $modified_date;
    }
    
    public function getPlate_status_id() {
        return $this->plate_status_id;
    }

    public function setPlate_status_id($plate_status_id) {
        $this->plate_status_id = $plate_status_id;
    }

    
    public function fillInData($array) {

        $this->plate_id = $array(PlateTable::plate_id);
        $this->advert_id = $array(PlateTable::advert_id);
        $this->plate_url = $array(PlateTable::plate_url);
        $this->plate_status_id = $array(PlateTable::plate_status_id);
        $this->create_date = $array(PlateTable::create_date);
        $this->modified_date = $array(PlateTable::modified_date);
    }

    public function getAsArray() {
        $array = array();
        $array[PlateTable::plate_id] = $this->getPlate_id();
        $array[PlateTable::advert_id] = $this->getAdvert_id();
        $array[PlateTable::plate_url] = $this->getPlate_url();
        $array[PlateTable::plate_status_id] = $this->getPlate_status_id();
        $array[PlateTable::create_date] = $this->getCreate_date();
        $array[PlateTable::modified_date] = $this->getModified_date();

        return $array;
    }

    public function addPlate($array) {
        $plate_id = $this->add(array(
            PlateTable::advert_id => $array[PlateTable::advert_id],
            PlateTable::plate_url => $array[PlateTable::plate_url]
        ));
        return $plate_id;
    }
}

?>
