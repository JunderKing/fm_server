<?php

include_once(dirname(__FILE__)."/../global.php");

class createGroup extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $title = $this->params['title'];
        $this->result = $this->model->createGroup($uid, $title);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$createGroup = new createGroup();
$createGroup->main();
