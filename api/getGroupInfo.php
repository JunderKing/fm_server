<?php

include_once(dirname(__FILE__)."/../global.php");

class getGroupInfo extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $group_id = $this->params['group_id'];
        $this->result = $this->model->getGroupInfo($uid, $group_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getGroupInfo = new getGroupInfo();
$getGroupInfo->main();
