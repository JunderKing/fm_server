<?php

include_once(dirname(__FILE__)."/../global.php");

class updateProjInfo extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $field = $this->params['field'];
        $value = $this->params['value'];
        $this->result = $this->model->updateProjInfo($uid, $proj_id, $field, $value);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$updateProjInfo = new updateProjInfo();
$updateProjInfo->main();
