<?php

include_once(dirname(__FILE__)."/../global.php");

class GetCardReply extends Api {
    function main(){
        $this->getParams();
        $comment_id = $this->params['comment_id'];
        $this->result = $this->model->getCardReply($comment_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getCardReply = new GetCardReply();
$getCardReply->main();
