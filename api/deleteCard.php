<?php

include_once(dirname(__FILE__)."/../global.php");

class deleteCard extends Api {
    function main(){
        $this->getParams();
        $card_id = $this->params['card_id'];
        $this->result = $this->model->deleteCard($card_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$deleteCard = new deleteCard();
$deleteCard->main();
