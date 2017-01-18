<?php

include_once(dirname(__FILE__)."/../global.php");

class getCardInfo extends Api {
    function main (){
        $this->getParams();
        $proj_id = $this->params['proj_id'];
        $canvas_id = $this->params['canvas_id'];
        $this->result = $this->model->getCardInfo($proj_id, $canvas_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getCardInfo = new getCardInfo();
$getCardInfo->main();
