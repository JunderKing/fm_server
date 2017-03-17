<?php

include_once "wxBizDataCrypt.php";


$appid = 'wx04a548f574c826fb';
$sessionKey = 'U1rZvd7TgFq2YWTgV1oDXw==';

$encryptedData="51x5tXPsg00ODI12fVAUHy2o4YA7RgYJn8se0/+Efk9gMdsi0S5iuDd42jObqfoD0wgfuxJImzeRLZEXSvJZgv+PsccbNVJVglRSmMq2khzCXrXkgFWjRcqPUprN5+M1mjAbVbHL9rqaM725Y1F48YvdOGlnaR6gQNq1p0w/APowK0WAQUlUmr/7e8MFj7g6O7+Avq0p7ZXIztrQgaRW24xxaVT7DjltX/6CbX4aCElT2YJ2HaNZj6OWGEaWp8LSBI9WlSb+zExqBTsO/QjjwJOIsiA/mL9wprlOKDou20j6FZrULWy6pwJ3wiQ69qitehHzlvLPylVqv1y1FDZjdaKi2PKQCi4GmRw7lK9a/SJAjFlqJJSYA8f64/e1+UGT+rVcERTNaPOl/ph5JsJg4Rvj1F/I8Fp+GfQkY6iFDbJH+4hRTBbfqGMWD0ANUZ5Spw6hK0M371eeId14J+Cm8LVw0jdminkhEqLX/HzSLIlzSbxpW/MJcnSv8GPc5HOo";


$iv = 'NXq/NEZkk6S+QNcSWZt8vg==';

$pc = new WXBizDataCrypt($appid, $sessionKey);
$errCode = $pc->decryptData($encryptedData, $iv, $data );

if ($errCode == 0) {
    print($data . "\n");
} else {
    print($errCode . "\n");
}
