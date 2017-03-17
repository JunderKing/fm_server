<?php

include_once(dirname(__FILE__)."/../global.php");

class changeCurGroup extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $group_id = $this->params['group_id'];
        $this->result = $this->model->changeCurGroup($uid, $group_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$changeCurGroup = new changeCurGroup();
$changeCurGroup->main();
