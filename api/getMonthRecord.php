<?php

include_once(dirname(__FILE__)."/../global.php");

class getMonthRecord extends Api {
    function main (){
        $this->getParams();
        $proj_id = $this->params['proj_id'];
        $this->result = $this->model->getMonthRecord($proj_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getMonthRecord = new getMonthRecord();
$getMonthRecord->main();
