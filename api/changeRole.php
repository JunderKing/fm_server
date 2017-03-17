<?php

include_once(dirname(__FILE__)."/../global.php");

class changeRole extends Api {
    function main (){
        $this->getParams();
        $uid = $this->params['uid'];
        $role = $this->params['role'];
        $this->result = $this->model->changeRole($uid, $role);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$changeRole = new changeRole();
$changeRole->main();
