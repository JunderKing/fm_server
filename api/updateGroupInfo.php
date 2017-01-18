<?php

include_once(dirname(__FILE__)."/../global.php");

class updateGroupInfo extends Api {
    function main (){
        $this->getParams();
        $group_id = $this->params['group_id'];
        $title = $this->params['title'];
        $this->result = $this->model->updateGroupInfo($group_id, $title);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$updateGroupInfo = new updateGroupInfo();
$updateGroupInfo->main();
