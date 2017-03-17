<?php

include_once(dirname(__FILE__)."/../global.php");

class getPulseInfo extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $pulse_id = $this->params['pulse_id'];
        $pulse_no = $this->params['pulse_no'];
        $this->result = $this->model->getPulseInfo($uid, $proj_id, $pulse_id, $pulse_no);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getPulseInfo = new getPulseInfo();
$getPulseInfo->main();

