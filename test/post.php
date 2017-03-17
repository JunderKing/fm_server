<?php

$appId = "wx04a548f574c826fb";
$appSecret = "68f1ef93cc6c9153e8d49b95899aa9a2";
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
$resData = file_get_contents($url);

 $resArr = json_decode($resData);
 $accessToken = $resArr->access_token;

$url = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=$accessToken";
$data = array('path'=>'pages/group/group?query=1', 'width'=>430);
print_r($accessToken);
$retdata = request_post($url, true, $data);
print_r($retdata);
 function request_post($url = '',$ispost=true, $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        
}
        
        $o = "";
        foreach ( $post_data as $k => $v  ) 
        { 
            $o.= "$k=" . urlencode( $v  ). "&" ;
         }
        $post_data = substr($o,0,-1);
        $key=md5(base64_encode($post_data));
        if($ispost){
            $url=$url;
        
}else{
            $url = $url.'?'.$post_data;
        
}
        
       
        $curlPost = 'key='.$key;
        header("Content-type: text/html; charset=utf-8");
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        if($ispost){
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        
}
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    
}
