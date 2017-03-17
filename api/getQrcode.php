<?php

include_once(dirname(__FILE__)."/../global.php");

class GetQrcode extends Api {
    function main(){
        $this->getParams();
        $path = $this->params['path'];
        $width = $this->params['width'];
        $this->result = $this->model->getQrcode($path, $width);
        if (is_int($this->result)) {
            $this->errmsg = $this->result;
            $this->result = '';
        }
        $this->output();
    }
}

$getQrcode = new GetQrcode();
$getQrcode->main();
