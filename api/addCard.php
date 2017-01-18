<?php

include_once(dirname(__FILE__)."/../global.php");

class addCard extends Api {
    function main(){
        $this->getParams();
        $proj_id = $this->params['proj_id'];
        $canvas_id = $this->params['canvas_id'];
        $title = $this->params['title'];
        $assumption = $this->params['assumption'];
        $this->result = $this->model->addCard($proj_id, $canvas_id, $title, $assumption);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$addCard = new addCard();
$addCard->main();
