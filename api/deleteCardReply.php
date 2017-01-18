<?php

include_once(dirname(__FILE__)."/../global.php");

class DeleteCardReply extends Api {
    function main(){
        $this->getParams();
        $reply_id = $this->params['reply_id'];
        $this->result = $this->model->deleteCardReply($reply_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$deleteCardReply = new DeleteCardReply();
$deleteCardReply->main();
