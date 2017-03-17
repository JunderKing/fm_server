<?php

include_once(dirname(__FILE__)."/../global.php");

class addComment extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $field = $this->params['field'];
        $field_id = $this->params['field_id'];
        $content = $this->params['content'];
        $this->result = $this->model->addComment($uid, $proj_id, $field, $field_id, $content);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$addComment = new addComment();
$addComment->main();
