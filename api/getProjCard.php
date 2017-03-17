<?php

include_once(dirname(__FILE__)."/../global.php");

class getProjCard extends Api {
    function main (){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $this->result = $this->model->getProjCard($uid, $proj_id);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getProjCard = new getProjCard();
$getProjCard->main();
