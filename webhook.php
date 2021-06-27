<?php
/*
این وب سرویس توسط تیم TelePro نوشته شده و اسکی بدون ذکر منبع حرام و نویسنده آن راضی نمیباشد !
نویسنده : @DevTelePro
کانال ما : @TeleProTM
*/
ob_start();
error_reporting(0);
header('Content-Type: application/json');
date_default_timezone_set('Asia/tehran');
##----------[functions]----------##
function checktoken($token){
    $res = json_decode(file_get_contents("https://api.telegram.org/bot$token/getme"),true);
    if($res['ok'] == 1){
        return true;
    }
}
##----------[getme]----------##
if($_GET['type'] == "getme"){
    if(checktoken($_GET['token']) == true){
        $res = json_decode(file_get_contents("https://api.telegram.org/bot".$_GET['token']."/getme"),true);
        $UserNameBot = $res["result"]["username"];
        $IDBot = $res["result"]["id"];
        $FirstBot = $res["result"]["first_name"];
        $type = $res['result']['is_bot'];
        print(json_encode(array('ok'=>true,'result'=>array('id'=>$IDBot,'is_bot'=>$type,'first_name'=>$FirstBot,'username'=>$UserNameBot,))));
    }else{
        print(json_encode(array('ok'=>false,'description'=>"Token Not Found!")));
    }
}
##----------[getme]----------##
if($_GET['type'] == "getwebhookinfo"){
    if(checktoken($_GET['token']) == true){
        $obj = json_decode(file_get_contents("https://api.telegram.org/bot".$_GET['token']."/getwebhookinfo"),true);
        $url = $obj['result']['url'];
        $certificate = $obj['result']['has_custom_certificate'];
        $panding = $obj['result']['pending_update_count'];
        $last_date = date('Y-m-d , H:i:s', $obj['result']['last_error_date']);
        $last_error = $obj['result']['last_error_message'];
        $max = $obj['result']['max_connections'];
        print(json_encode(array('ok'=>true,'result'=>array('url'=>$url,'has_custom_certificate'=>$certificate,'pending_update_count'=>$panding,'last_error_date'=>$last_date,'last_error_message'=>$last_error,'max_connections'=>$max))));
    }else{
        print(json_encode(array('ok'=>false,'description'=>"Token Not Found!")));
    }
}
##----------[setwebhook]----------##
if($_GET['type'] == "setwebhook"){
    if(checktoken($_GET['token']) == true){
        $api = json_decode(file_get_contents("https://api.telegram.org/bot".$_GET['token']."/setwebhook?url=".$_GET['url']),true);
        print(json_encode(array('ok'=>true,'result'=>$api['result'],'description'=>$api['description'])));
    }else{
        print(json_encode(array('ok'=>false,'description'=>"Token Not Found!")));
    }
}
##----------[deletewebhook]----------##
if($_GET['type'] == "deletewebhook"){
    if(checktoken($_GET['token']) == true){
        $api = json_decode(file_get_contents("https://api.telegram.org/bot".$_GET['token']."/deletewebhook"),true);
        print(json_encode(array('ok'=>true,'result'=>$api['result'],'description'=>$api['description'])));
    }else{
        print(json_encode(array('ok'=>false,'description'=>"Token Not Found!")));
    }
}
##----------[END SOURCE]----------##
/*
این وب سرویس توسط تیم TelePro نوشته شده و اسکی بدون ذکر منبع حرام و نویسنده آن راضی نمیباشد !
نویسنده : @DevTelePro
کانال ما : @TeleProTM
*/