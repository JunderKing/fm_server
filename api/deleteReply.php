<?php

include_once(dirname(__FILE__)."/../global.php");

class DeleteReply extends Api {
    function main(){
        $this->getParams();
        $reply_id = $this->params['reply_id'];
        $this->result = $this->model->deleteReply($reply_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$deleteReply = new DeleteReply();
$deleteReply->main();
