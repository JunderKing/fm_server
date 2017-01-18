<?php

include_once(dirname(__FILE__)."/../global.php");

class getCanvasInfo extends Api {
    function main(){
        $this->getParams();
        $proj_id = $this->params['proj_id'];
        $this->result = $this->model->getCanvasInfo($proj_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getCanvasInfo = new getCanvasInfo();
$getCanvasInfo->main();

