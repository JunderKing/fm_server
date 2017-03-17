<?php

include_once(dirname(__FILE__)."/../global.php");

class getMyProject extends Api {
    function main (){
        $this->getParams();
        $uid = $this->params['uid'];
        $this->result = $this->model->getMyProject($uid);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getMyProject = new getMyProject();
$getMyProject->main();
