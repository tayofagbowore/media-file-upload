<?php

class FileUpload {
    const ERROR = 'error';
    const TYPE = 'type';
    const NAME = 'name';
    const SIZE = 'size';
    const TMP_NAME = 'tmp_name';
    const MAX_SIZE = 10485760; //10MB

    const STATUS_OK = 1;
    const STATUS_ERROR = 2;
    const STATUS_DISALLOWED_MIME_TYPE = 3;
    const STATUS_DISALLOWED_EXT = 4;
    const STATUS_FILE_EXISTS = 5;
    const STATUS_MAX_SIZE_EXCEEDED = 6;

    private $allowed_exts;
    private $allowed_mime;
    private $file_input_name;
    private $max_size;

    public function __construct($file_input_name, $allowed_exts, $allowed_mime, $max_size=NULL){
        $this->file_input_name = $file_input_name;
        $this->allowed_exts = $allowed_exts;
        $this->allowed_mime = $allowed_mime;

        $this->max_size = ($max_size != NULL) ? $max_size : FileUpload::MAX_SIZE;
    }

    private function hasAllowedMimeType($index = NULL){
        if($index === NULL){
            return in_array(strtolower($_FILES[$this->file_input_name][FileUpload::TYPE]), $this->allowed_mime);
        }
        return in_array(strtolower($_FILES[$this->file_input_name][FileUpload::TYPE][$index]), $this->allowed_mime);
    }

    public function mimeType($file_input_name){
        return (strtolower($_FILES[$file_input_name][FileUpload::TYPE]));
    }

    private function hasAllowedExt($index = NULL){
        if ($index === NULL) {
            $temp = explode('.', strtolower($_FILES[$this->file_input_name][FileUpload::NAME]));
            return in_array(end($temp), $this->allowed_exts);
        }
        $temp = explode('.', strtolower($_FILES[$this->file_input_name][FileUpload::NAME][$index]));
        return in_array(end($temp), $this->allowed_exts);
    }

    private function fileExists($file_name, $dir_path){
//        $dir_path = ($dir_path[strlen($dir_path) - 1] == '/') ? $dir_path : $dir_path . '/';
        return file_exists($dir_path.$file_name);
    }

    private function hasExceededMaxSize($index = NULL){
        if ($index === NULL) {
            return $_FILES[$this->file_input_name][FileUpload::SIZE] > FileUpload::MAX_SIZE;
        }
        return $_FILES[$this->file_input_name][FileUpload::SIZE][$index] > FileUpload::MAX_SIZE;
    }

    public function saveFile($file_name, $dir_path, $index = NULL){
        $dir_path = ($dir_path[strlen($dir_path) - 1] == '/') ? $dir_path : $dir_path . '/';
        if ($this->fileExists($file_name, $dir_path)){
            return FileUpload::STATUS_FILE_EXISTS;
        }else if (!$this->hasAllowedMimeType($index)){
            return FileUpload::STATUS_DISALLOWED_MIME_TYPE;

        }else if (!$this->hasAllowedExt($index)){
            return FileUpload::STATUS_DISALLOWED_EXT;
        }else if ($this->hasExceededMaxSize($index)){
            return FileUpload::STATUS_MAX_SIZE_EXCEEDED;
        }else{
            move_uploaded_file($_FILES[$this->file_input_name][FileUpload::TMP_NAME][$index], $dir_path.$file_name);
            return FileUpload::STATUS_OK;
        }

    }


}