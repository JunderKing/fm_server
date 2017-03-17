<?php

include_once(dirname(__FILE__)."/../global.php");

class upwardCard extends Api {
    function main(){
        $this->getParams();
        $proj_id = $this->params['proj_id'];
        $card_id = $this->params['card_id'];
        $this->result = $this->model->upwardCard($proj_id, $card_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$upwardCard = new upwardCard();
$upwardCard->main();
