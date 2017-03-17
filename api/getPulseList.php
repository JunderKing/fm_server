<?php

include_once(dirname(__FILE__)."/../global.php");

class getPulseList extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $pulse_id = $this->params['pulse_id'];
        $this->result = $this->model->getPulseList($uid, $proj_id, $pulse_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getPulseList = new getPulseList();
$getPulseList->main();
