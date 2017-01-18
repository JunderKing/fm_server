<?php

include_once(dirname(__FILE__)."/../global.php");

class AddCardReply extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $comment_id = $this->params['comment_id'];
        $content = $this->params['content'];
        $this->result = $this->model->addCardReply($uid, $comment_id, $content);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$addClassReply = new AddCardReply();
$addClassReply->main();
