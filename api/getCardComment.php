<?php

include_once(dirname(__FILE__)."/../global.php");

class GetCardComment extends Api {
    function main(){
        $this->getParams();
        $card_id = $this->params['card_id'];
        $this->result = $this->model->getCardComment($card_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getCardComment = new GetCardComment();
$getCardComment->main();
        
