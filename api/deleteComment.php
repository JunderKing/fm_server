<?php

include_once(dirname(__FILE__)."/../global.php");

class DeleteComment extends Api {
    function main(){
        $this->getParams();
        $comment_id = $this->params['comment_id'];
        $this->result = $this->model->deleteComment($comment_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$deleteComment = new DeleteComment();
$deleteComment->main();
