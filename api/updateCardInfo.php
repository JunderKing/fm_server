<?php

include_once(dirname(__FILE__)."/../global.php");

class updateCardInfo extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $card_id = $this->params['card_id'];
        $title = $this->params['title'];
        $assumption = $this->params['assumption'];
        $result = $this->params['result'];
        $status = $this->params['status'];
        $this->result = $this->model->updateCardInfo($uid, $card_id, $title, $assumption, $result, $status);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$updateCardInfo = new updateCardInfo();
$updateCardInfo->main();
