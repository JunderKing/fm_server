<?php

include_once(dirname(__FILE__)."/../global.php");

class deleteGroupProj extends Api {
    function main (){
        $this->getParams();
        $group_id = $this->params['group_id'];
        $proj_id = $this->params['proj_id'];
        $this->result = $this->model->deleteGroupProj($group_id, $proj_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$deleteGroupProj = new deleteGroupProj();
$deleteGroupProj->main();
