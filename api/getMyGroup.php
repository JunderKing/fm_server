<?php

include_once(dirname(__FILE__)."/../global.php");

class getMyGroup extends Api {
    function main (){
        $this->getParams();
        $uid = $this->params['uid'];
        $this->result = $this->model->getMyGroup($uid);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getMyGroup = new getMyGroup();
$getMyGroup->main();
