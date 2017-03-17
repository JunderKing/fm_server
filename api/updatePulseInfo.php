<?php

include_once(dirname(__FILE__)."/../global.php");

class updatePulseInfo extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $pulse_id = $this->params['pulse_id'];
        $pulse_no = $this->params['pulse_no'];
        $content = $this->params['content'];
        $this->result = $this->model->updatePulseInfo($uid, $proj_id, $pulse_id, $pulse_no, $content);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$updatePulseInfo = new updatePulseInfo();
$updatePulseInfo->main();
