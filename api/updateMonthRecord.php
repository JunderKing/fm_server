<?php

include_once(dirname(__FILE__)."/../global.php");

class updateMonthRecord extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $month_no = $this->params['month_no'];
        $content = $this->params['content'];
        $pdf_url = $this->params['pdf_url'];
        $this->result = $this->model->updateMonthRecord($uid, $proj_id, $month_no, $content, $pdf_url);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getMonthRecord = new updateMonthRecord();
$getMonthRecord->main();
