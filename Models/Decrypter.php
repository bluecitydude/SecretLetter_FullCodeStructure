<?php
namespace Models;
class Decrypter{
    public function decrypt($secret,$passphrase,int $times){
        for($times;$times>0;--$times){
                if(strlen($secret)>10000000) return "Message Length exceeded characters, try again with a shorter message";
                
                $data=base64_decode($secret);        
                if(!$data) return false;
                $salt=substr($data,0,16);
                $iv=substr($data,16,12);
                $tag=substr($data,28,16);
                $encrypted=substr($data,44);
                                
                $key=hash_pbkdf2('sha256',$passphrase,$salt,100000,32,true);
                $decrypted=openssl_decrypt($encrypted,"aes-256-gcm",$key,OPENSSL_RAW_DATA,$iv,$tag);

                if($decrypted===false){
                    return "failed";
                }
                $secret=$decrypted;    
            }
        return htmlspecialchars($secret);
    }
}
