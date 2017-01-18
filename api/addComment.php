<?php

include_once(dirname(__FILE__)."/../global.php");

class addComment extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $target_id = $this->params['target_id'];
        $target_type = $this->params['target_type'];
        $content = $this->params['content'];
        $this->result = $this->model->addComment($uid, $proj_id, $target_id, $target_type, $content);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$addComment = new addComment();
$addComment->main();
