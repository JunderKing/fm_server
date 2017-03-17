<?php

include_once(dirname(__FILE__)."/../global.php");

class getMarker extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $this->result = $this->model->getMarker($uid, $proj_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getMarker = new getMarker();
$getMarker->main();
