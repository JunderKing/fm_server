<?php

include_once(dirname(__FILE__)."/../global.php");

class getProjInfo extends Api {
    function main(){
        $this->getParams();
        $proj_id = $this->params['proj_id'];
        $this->result = $this->model->getProjInfo($proj_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getProjInfo = new getProjInfo();
$getProjInfo->main();
