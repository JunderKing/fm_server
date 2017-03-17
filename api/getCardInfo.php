<?php

include_once(dirname(__FILE__)."/../global.php");

class getCardInfo extends Api {
    function main (){
        $this->getParams();
        $uid = $this->params['uid'];
        $card_id = $this->params['card_id'];
        $this->result = $this->model->getCardInfo($uid, $card_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getCardInfo = new getCardInfo();
$getCardInfo->main();
