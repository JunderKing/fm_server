<?php

include_once(dirname(__FILE__)."/../global.php");

class AddCardComment extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $card_id = $this->params['card_id'];
        $content = $this->params['content'];
        $this->result = $this->model->addCardComment($uid, $card_id, $content);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$addCardComment = new AddCardComment();
$addCardComment->main();
