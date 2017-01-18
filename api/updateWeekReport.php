<?php

include_once(dirname(__FILE__)."/../global.php");

class updateWeekReport extends Api {
    function main(){
        $this->getParams();
        $uid = $this->params['uid'];
        $proj_id = $this->params['proj_id'];
        $week_no = $this->params['week_no'];
        $content = $this->params['content'];
        $this->result = $this->model->updateWeekReport($uid, $proj_id, $week_no, $content);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$updateWeekReport = new updateWeekReport();
$updateWeekReport->main();
