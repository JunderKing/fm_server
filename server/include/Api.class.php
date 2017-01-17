<?php

class Api {
    protected $params = array();
    protected $result;
    protected $errmsg;
    protected $model;
    public function __construct(){
        $this->model = new Model();
    }
    protected function getParams(){
        foreach ($_REQUEST as $key=>$value) {
            $this->params[$key] = $value;
        }
    }
    protected function output(){
        if ($this->errmsg) {
            $this->result = array(
                "error"=>$this->errmsg
            );
        }
        if (is_int($this->result)) {
            $this->result = array(
                "ret"=>$this->result
            );
        }
        echo json_encode($this->result);
        exit;
    }
}
