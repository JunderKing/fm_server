<?php

include_once(dirname(__FILE__)."/../global.php");

class updateCanvasInfo extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $canvas_id = $this->params['canvas_id'];
        $content = $this->params['content'];
        $this->result = $this->model->updateCanvasInfo($uid, $proj_id, $canvas_id, $content);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$updateCanvasInfo = new updateCanvasInfo();
$updateCanvasInfo->main();
