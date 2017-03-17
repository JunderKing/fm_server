<?php

include_once(dirname(__FILE__)."/../global.php");

class login extends Api {
    function main(){
        $this->getParams();
        $code = $this->params['code'];
        $raw_data = $this->params['raw_data'];
        $iv = $this->params['iv'];
        $this->result = $this->model->login($code, $raw_data, $iv);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$login = new login();
$login->main();
