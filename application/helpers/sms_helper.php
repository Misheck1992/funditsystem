<?php
function send_sms($reciever,$sms){
    $endpoint = "https://telcomw.com/api-v2/send";
    $message = trim($sms). "\r\nFinance Realm Malawi  (Customer care:0994099461)";
    $ch = curl_init();
    $array_post = http_build_query(array(
        'text'=>$message,
        'numbers'=>$reciever,
        'api_key'=>'OAIB4XSVA9WSYLXMVTPQ',
        'password'=>'Kaya',
        'from'=>'Finrealm'
    ));

    curl_setopt($ch, CURLOPT_URL,$endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$array_post);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);

    curl_close ($ch);
    return $server_output;
}
function send_sms1($reciever,$sms){
    $endpoint = "https://telcomw.com/api-v2/send";
    $message = trim($sms). "\r\n\r\n--TB Info System--";
    $ch = curl_init();
    $array_post = http_build_query(array(
        'text'=>$message,
        'numbers'=>$reciever,
        'api_key'=>'OAIB4XSVA9WSYLXMVTPQ',
        'password'=>'Kaya',
        'from'=>'Finrealm'
    ));

    curl_setopt($ch, CURLOPT_URL,$endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$array_post);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);

    curl_close ($ch);
    return $server_output;
}
function get_balance(){

    $endpoint = "https://api.smsdeliveryapi.xyz/balance";

    $ch = curl_init();
    $array_post = http_build_query(array(
        'api_key'=>'OAIB4XSVA9WSYLXMVTPQ',
        'password'=>'smartpassword#1'
    ));

    curl_setopt($ch, CURLOPT_URL,$endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$array_post);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);

    curl_close ($ch);

    return $server_output;
}

?>
