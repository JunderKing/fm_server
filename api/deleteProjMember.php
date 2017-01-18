<?php

include_once(dirname(__FILE__)."/../global.php");

class deleteProjMember extends Api {
    function main(){
        $this->getParams();
        $proj_id = $this->params['proj_id'];
        $uid = $this->params['uid'];
        $this->result = $this->model->deleteProjMember($proj_id, $uid);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$deleteProjMember = new deleteProjMember();
$deleteProjMember->main();
