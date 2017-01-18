<?php

include_once(dirname(__FILE__)."/../global.php");

class createProj extends Api {
    function main (){
        $this->getParams();
        $uid = $this->params['uid'];
        $role = $this->params['role'];
        $title = $this->params['title'];
        $intro = $this->params['intro'];
        $mission = $this->params['mission'];
        $vision = $this->params['vision'];
        $value = $this->params['value'];
        $this->result = $this->model->createProj($uid, $role, $title, $intro, $mission, $vision, $value);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$createProj = new createProj();
$createProj->main();
