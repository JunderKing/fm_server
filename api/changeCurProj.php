<?php

include_once(dirname(__FILE__)."/../global.php");

class changeCurProj extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $this->result = $this->model->changeCurProj($uid, $proj_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$changeCurProj = new changeCurProj();
$changeCurProj->main();
