<?php

ob_start();
error_reporting(0);
//==========[config]==========//
define('API_KEY','');
$admins = array("499816482");
//==========[method]==========//
function TelePro($method, $datas = []){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}
//==========[variables]==========//
@$update = json_decode(file_get_contents('php://input'),true);
if(isset($update['message'])){
    @$message = $update['message']; 
    @$chat_id = $message['chat']['id'];
    @$text = $message['text'];
    @$message_id = $message['message_id'];
    @$from_id = $message['from']['id'];
}
//==========[getChatMember]==========//
/*$truechannel = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@UzBotsPay_Bot&user_id=".$from_id),true);
$tch = $truechannel['result']['status'];*/
//==========[json]==========//
@$users = json_decode(file_get_contents("user/$from_id.json"),true);
@$settings = json_decode(file_get_contents("data/settings.json"),true);
//==========[channel lock]==========//
/*if(!in_array($tch,["member","administrator","creator"])){
    TelePro('sendMessage',[
    'chat_id'=>$chat_id,
    'text'=>"ربات قفل میباشد !\nجهت ادامه فعالیت در ربات لطفا در کانال @TeleProTM عضو شوید!\nسپس دستور /start را ارسال نمایید.",
    'message_id'=>$message_id,
    'reply_markup'=>json_encode(['KeyboardRemove'=>[
        ],'remove_keyboard'=>true])
    ]);
    exit();
}*/
//==========[start]==========//
if(preg_match('/^\/([Ss]tart)/',$text)){
    TelePro('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"Bo'limlardan birini tanlang",
        'message_id'=>$message_id,
        'reply_markup'=>json_encode(['keyboard'=>[
            [['text'=>"O'rnatish va o'rnatish"],['text'=>"delete webhook"]],
            [['text'=>"Token haqida ma'lumot"],['text'=>"Ma'lumot va fikrlar"]],
            [['text'=>"/panel"]]
            ],'resize_keyboard'=>true])
        ]);
}
//==========[back]==========//
elseif($text == "orqaga"){
    unlink("user/$from_id.json");
    TelePro('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"Men orqaga asosiy menyuda bo'ldim :)",
        'message_id'=>$message_id,
        'reply_markup'=>json_encode(['keyboard'=>[
            [['text'=>"setwebhook"],['text'=>"delete webhook"]],
            [['text'=>"Token haqida ma'lumot"],['text'=>"Ma'lumot va fikrlar"]],
            [['text'=>"/panel"]]
            ],'resize_keyboard'=>true])
        ]);    
}
//==========[setwebhook]==========//
elseif($text == "setwebhook"){
    $users['command'] = "setwebhook-token";
    file_put_contents("user/$from_id.json",json_encode($users));
    TelePro('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"Iltimos, kerakli robot tokenini yuboring:",
        'message_id'=>$message_id,
        'reply_markup'=>json_encode(['keyboard'=>[
            [['text'=>"orqaga"]]
            ],'resize_keyboard'=>true])
        ]);    
}
elseif($users['command'] == "setwebhook-token"){
    $api = json_decode(file_get_contents("https://".$_SERVER['HTTP_HOST']."/".str_replace("bot","webhook",$_SERVER['SCRIPT_NAME'])."?type=getme&token=".$text),true);
    $result = $api['ok'];
    if($result == 1){
        $users['command'] = "setwebhook-url";
        $users['token'] = "$text";
        file_put_contents("user/$from_id.json",json_encode($users));
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Sizning belgi to'g'ri edi!
O'rnatish operatsiyasini amalga oshirish uchun URL manzilini kiriting:",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);  
    }else{
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Siz yuborgan token noto‘g‘ri!",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);  
    }
}
elseif($users['command'] == "setwebhook-url"){
    if (!preg_match("/\b(?:(?:https|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$text)){
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Siz yuborgan URL manzili noto'g'ri! Lط nAltimos URL manzilini https bilan birga yuboring:",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);
    }else{
        $users['command'] = "null";
        $users['url'] = "$text";
        file_put_contents("user/$from_id.json",json_encode($users));
        $token = $users['token'];
        $url = $text;
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Kiritilgan manzil to'g'ri edi! \n Your token: \n $token\nآSsenariy darsingiz :\n$url",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"Setni tasdiqlang va sozlang va tekshiring"]],
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);
    }
}
elseif($text == "Setni tasdiqlang va sozlang va tekshiring"){
    $token = $users['token'];
    $url = $users['url'];
    $api = json_decode(file_get_contents("https://".$_SERVER['HTTP_HOST']."/".str_replace("bot","webhook",$_SERVER['SCRIPT_NAME'])."?type=setwebhook&token=".$token."&url=".$url),true);
    $result = $api['description'];
    if($result == "Webhook is already set"){
        $results = "Ilgari manzili bo'lgan robot \n$url\nBu o'rnatildi!";
    }
    if($result == "Webhook was set"){
        $results = "Muvaffaqiyatli sizning robotingiz manzil bilan \n$url\nست شد !";
    }
    TelePro('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>$results,
        'message_id'=>$message_id,
        'reply_markup'=>json_encode(['keyboard'=>[
            [['text'=>"orqaga"]]
            ],'resize_keyboard'=>true])
        ]);
}
//==========[deletewebhook]==========//
elseif($text == "delete webhook"){
    $users['command'] = "deletewebhook-token";
    file_put_contents("user/$from_id.json",json_encode($users));
    TelePro('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"Iltimos, kerakli robot tokenini yuboring:",
        'message_id'=>$message_id,
        'reply_markup'=>json_encode(['keyboard'=>[
            [['text'=>"orqaga"]]
            ],'resize_keyboard'=>true])
        ]);    
}
elseif($users['command'] == "deletewebhook-token"){
    $users['command'] = "null";
    file_put_contents("user/$from_id.json",json_encode($users));
    $api = json_decode(file_get_contents("https://".$_SERVER['HTTP_HOST']."/".str_replace("bot","webhook",$_SERVER['SCRIPT_NAME'])."?type=deletewebhook&token=".$text),true);
    if($api['description'] == "Webhook was deleted"){
        $results = "Token tarixingiz tozalandi!";
    }
    if($api['description'] == "Webhook is already deleted"){
        $results = "Belgining tarixi bo'sh edi!";
    }
    if($api['description'] == "Token Not Found!"){
        $results = "Siz yuborgan token noto‘g‘ri!";
    }    
    if($api['ok'] == 1){
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>$results,
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);  
    }else{
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>$results,
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);  
    }
}
//==========[getme]==========//
elseif($text == "Token haqida ma'lumot"){
    $users['command'] = "getme";
    file_put_contents("user/$from_id.json",json_encode($users));
    TelePro('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"Iltimos, kerakli robot tokenini yuboring:",
        'message_id'=>$message_id,
        'reply_markup'=>json_encode(['keyboard'=>[
            [['text'=>"orqaga"]]
            ],'resize_keyboard'=>true])
        ]);    
}
elseif($users['command'] == "getme"){
    $users['command'] = "null";
    file_put_contents("user/$from_id.json",json_encode($users));
    $api = json_decode(file_get_contents("https://".$_SERVER['HTTP_HOST']."/".str_replace("bot","webhook",$_SERVER['SCRIPT_NAME'])."?type=getme&token=".$text),true);
    $id = $api['result']['id'];
    $is_bot = $api['result']['is_bot'];
    $first_name = $api['result']['first_name'];
    $username = $api['result']['username'];
    if($api['ok'] == 1){
        if($is_bot == 1){
            $is_bot = "true";
        }else{
            $is_bot = "false";
        }
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Token haqida ma'lumotSiz kiritgan quyidagilar:\nbot idsi:$id\nis_bot:$is_bot\nbot nomi:$first_name\nbot usernemesi:@$username",
            'message_id'=>$message_id,
            ]);  
    }else{
        if($api['description'] == "Token Not Found!"){
            $results = "Siz yuborgan token noto‘g‘ri!";
        }    
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>$results,
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);  
    }
}
//==========[getwebhookinfo]==========//
elseif($text == "Ma'lumot va fikrlar"){
    $users['command'] = "getwebhookinfo";
    file_put_contents("user/$from_id.json",json_encode($users));
    TelePro('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"Iltimos, kerakli robot tokenini yuboring:",
        'message_id'=>$message_id,
        'reply_markup'=>json_encode(['keyboard'=>[
            [['text'=>"orqaga"]]
            ],'resize_keyboard'=>true])
        ]);    
}
elseif($users['command'] == "getwebhookinfo"){
    $users['command'] = "null";
    file_put_contents("user/$from_id.json",json_encode($users));
    $info = json_decode(file_get_contents("https://".$_SERVER['HTTP_HOST']."/".str_replace("bot","webhook",$_SERVER['SCRIPT_NAME'])."?type=getme&token=".$text),true);
    if($info['ok'] == 1){
        date_default_timezone_set('Asia/tehran');
        $api = json_decode(file_get_contents("https://".$_SERVER['HTTP_HOST']."/".str_replace("bot","webhook",$_SERVER['SCRIPT_NAME'])."?type=getwebhookinfo&token=".$text),true);
        $url = $api['result']['url'];
        $certificate = $api['result']['has_custom_certificate'];
        $panding = $api['result']['pending_update_count'];
        $last_date = date('Y-m-d , H:i:s', $api['result']['last_error_date']);
        $last_error = $api['result']['last_error_message'];
        $max = $api['result']['max_connections'];
        if($url == "" || $url == null){
            $url = "--";
        }
        if($last_error == "" || $last_error == null){
            $last_error = "--";
            $last_date = "--";
        }
        if($max == "" || $max == null){
            $max = "--";
        }
        if($certificate == false || $certificate != 1){
            $certificate = "--";
        }
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Siz kiritgan token ma'lumotlari quyidagicha:\nmanzil: $url\nsert: $certificate\nbuyru: $panding\noxir xato: $last_date\nso'ngi xat: $last_error\nmaxula: $max
            ",
            'message_id'=>$message_id,
            ]);  
    }else{
        $api =[];
        if($api['description'] == "Token Not Found!"){
            $results = "Siz yuborgan token noto‘g‘ri!";
        }    
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>$results,
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);  
    }
}

//==========[admin panel]==========//
elseif($text == "/panel" || $text == "Boshqaruv menyusi"){
    if(in_array($from_id,$admins)){
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Ma'mur paneliga muvaffaqiyatli kiritildi!!",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"Statistika"]],
                [['text'=>"Hamma uchun ochiq"],['text'=>"Ommaviy oldinga"]],
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);    
    }else{
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Siz menejerlardan boshqa hech narsa emassiz!",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);          
    }
}
//==========[state]==========//
elseif($text == "Statistika"){
    if(in_array($from_id,$admins)){
        $count = count($settings['members']);
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Robot a'zolari tengdir: $count",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"Boshqaruv menyusi"]]
                ],'resize_keyboard'=>true])
            ]);   
    }else{
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Siz menejerlardan boshqa hech narsa emassiz!",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);      
    }
}
//==========[forall]==========//
elseif($text == "Ommaviy oldinga"){
    if(in_array($from_id,$admins)){
        $users['command'] = "for-all";
        file_put_contents("user/$from_id.json",json_encode($users));    
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Istalgan xabarni barcha a'zolarga yo'naltirish uchun yuboring:",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"Boshqaruv menyusi"]]
                ],'resize_keyboard'=>true])
            ]);   
    }else{
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Siz menejerlardan boshqa hech narsa emassiz!",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);      
    }
}
elseif($users['command'] == "for-all"){
    if($text != "Boshqaruv menyusi"){
        $users['command'] = "null";
        file_put_contents("user/$from_id.json",json_encode($users)); 
        foreach($settings['members'] as $all){
            TelePro('ForwardMessage',[
                'chat_id'=>$all,
                'from_chat_id'=>$chat_id,
                'message_id'=>$message_id
                ]);
        }
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Sizning xabaringiz barcha a'zolarga muvaffaqiyatli yuborildi!",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"Boshqaruv menyusi"]]
                ],'resize_keyboard'=>true])
            ]);   
    }
}
//==========[sendall]==========//
elseif($text == "Hamma uchun ochiq"){
    if(in_array($from_id,$admins)){
        $users['command'] = "send-all";
        file_put_contents("user/$from_id.json",json_encode($users));    
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Istalgan xabarni barcha a'zolarga yuboring:",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"Boshqaruv menyusi"]]
                ],'resize_keyboard'=>true])
            ]);   
    }else{
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Siz menejerlardan boshqa hech narsa emassiz!",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"orqaga"]]
                ],'resize_keyboard'=>true])
            ]);      
    }
}
elseif($users['command'] == "send-all"){
    if($text != "Boshqaruv menyusi"){
        $users['command'] = "null";
        file_put_contents("user/$from_id.json",json_encode($users)); 
        foreach($settings['members'] as $all){
            if($text != null){
                TelePro('sendMessage',[
                    'chat_id'=>$all,
                    'text'=>$text,
                    'message_id'=>$message_id,
                    'reply_markup'=>json_encode(['keyboard'=>[
                        [['text'=>"orqaga"]]
                        ],'resize_keyboard'=>true])
                        ]);  
            }
        }
        TelePro('sendMessage',[
            'chat_id'=>$chat_id,
            'text'=>"Sizning xabaringiz barcha a'zolarga muvaffaqiyatli yuborildi!",
            'message_id'=>$message_id,
            'reply_markup'=>json_encode(['keyboard'=>[
                [['text'=>"Boshqaruv menyusi"]]
                ],'resize_keyboard'=>true])
            ]);   
    }
}
//==========[save data]==========//
if(!file_exists("user/$from_id.json")){
    $users['command'] = "null";
    file_put_contents("user/$from_id.json",json_encode($users));
}
if(!in_array($from_id,$settings['members'])){
    $settings['members'][] = $from_id;
    file_put_contents("data/settings.json",json_encode($settings));
}
//==========[error unlink]==========//
if(file_exists("error_log")){
    unlink("error_log");
}

?>