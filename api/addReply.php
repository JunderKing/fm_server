<?php

include_once(dirname(__FILE__)."/../global.php");

class AddReply extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $comment_id = $this->params['comment_id'];
        $content = $this->params['content'];
        $this->result = $this->model->addReply($uid, $comment_id, $content);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$addReply = new AddReply();
$addReply->main();
