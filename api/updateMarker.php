<?php

include_once(dirname(__FILE__)."/../global.php");

class updateMarker extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $field = $this->params['field'];
        $field_id = $this->params['field_id'];
        $this->result = $this->model->updateMarker($uid, $proj_id, $field, $field_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$updateMarker = new updateMarker();
$updateMarker->main();
