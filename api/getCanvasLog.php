<?php

include_once(dirname(__FILE__)."/../global.php");

class getCanvasLog extends Api {
    function main (){
        $this->getParams();
        $proj_id = $this->params['proj_id'];
        $canvas_id = $this->params['canvas_id'];
        $this->result = $this->model->getCanvasLog($proj_id, $canvas_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getCanvasLog = new getCanvasLog();
$getCanvasLog->main();
