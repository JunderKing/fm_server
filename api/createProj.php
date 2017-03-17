<?php

include_once(dirname(__FILE__)."/../global.php");

class createProj extends Api {
    function main (){
        $this->getParams();
        $uid = $this->params['uid'];
        $title = $this->params['title'];
        $intro = $this->params['intro'];
        $vision = $this->params['vision'];
        $this->result = $this->model->createProj($uid, $title, $intro, $vision);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        };
        $this->output();
    }
}

$createProj = new createProj();
$createProj->main();
