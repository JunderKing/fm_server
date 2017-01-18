<?php

include_once(dirname(__FILE__)."/../global.php");

class updateSummary extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $content = $this->params['content'];
        $photo_url = $this->params['photo_url'];
        $this->result = $this->model->updateSummary($uid, $proj_id, $content, $photo_url);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$updateSummary = new updateSummary();
$updateSummary->main();
