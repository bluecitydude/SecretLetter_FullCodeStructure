<?php
header('Content-Type:application/json;');
require_once "../Middleware/RateLimiter.php";
require_once "../Models/Encrypter.php";
require_once "../Models/Decrypter.php";
use Middleware\RateLimiter;

$limiter=new RateLimiter();
if(!($limiter->handleRequest())){
    send_output("failed","Rate Limit Exceeded");
    exit;
}
use Models\Encrypter;
use Models\Decrypter;
function check_is_set($input,$passphrase,$times):bool{
    if(isset($input)&& isset($passphrase) && isset($times)) return true; 
    else return false;
}

function send_output($status,$response){
    return json_encode([
        "status"=>$status,
        "response"=>($response."--".$_SERVER['HTTP_ORIGIN'])
    ]);
}

function check_layers($time){return $time>5;}
$input=json_decode(file_get_contents("php://input"),true);

if(!$input) $input=$_POST;

$mode=$input['mode']??'';
if(isset($mode)){
    if($mode==="encrypt"){
        $message=$input['message']??'';
        $passphrase=$input['passphrase']??'';
        $times=$input['times']??'';

        if(check_is_set($message,$passphrase,$times && check_layers($times))){
            $encrypt=new Encrypter();
            $encrypted= $encrypt->encrypt($message,$passphrase,$times);

        if($encrypted!==false)  echo send_output("success",$encrypted);
    
        else echo send_output('failed','something is wrong, try again later');
        }
        else{
            echo send_output('failed','Enter every field properly and/or correctly');
        }
    }

    else if($mode==='decrypt'){

        $secret=$input['secret']??'';
        $passphrase=$input['passphrase']??'';
        $times=($input['times'])??'';

        if(check_is_set($secret,$passphrase,$times) && check_layers($times)){  
            $times=$times;
            $decrypt=new Decrypter();
            $decrypted=$decrypt->decrypt($secret,$passphrase,$times);

            if($decrypted!==false) {echo send_output("success",$decrypted);}
            else echo send_output("failed",'something is wrong, try again later');
        }
        else echo send_output("failed","Enter every field properly and/or correctly");
    }
    else{
     echo send_output("failed","Invalid mode");   
    }
}
else{
   echo send_output("failed","Mode missing"); 
}