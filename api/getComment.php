<?php

include_once(dirname(__FILE__)."/../global.php");

class GetComment extends Api {
    function main(){
        $this->getParams();
        $proj_id = $this->params['proj_id'];
        $target_id = $this->params['target_id'];
        $target_type = $this->params['target_type'];
        $this->result = $this->model->getComment($proj_id, $target_id, $target_type);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getComment = new GetComment();
$getComment->main();
