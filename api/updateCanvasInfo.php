<?php

include_once(dirname(__FILE__)."/../global.php");

class updateCanvasInfo extends Api {
    function main(){
        $this->getParams();
        $proj_id = $this->params['proj_id'];
        $field = $this->params['field'];
        $content = $this->params['content'];
        $this->result = $this->model->updateCanvasInfo($proj_id, $field, $content);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$updateCanvasInfo = new updateCanvasInfo();
$updateCanvasInfo->main();
