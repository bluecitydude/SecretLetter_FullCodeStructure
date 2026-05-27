<?php
namespace Models;
class Encrypter{
    public function encrypt($message,$passphrase,int $times){
        if(strlen($message)>1000) return "Message Length exceeded characters, try again with a shorter message";
        $key='';$iv='';$tag='';    
        for($times;$times>0;$times--){
            if(strlen($message)>10000000) return "Message Length exceeded characters, try again with a shorter message";
            $salt=random_bytes(16);
           $key=hash_pbkdf2("sha256",
                            $passphrase,
                            $salt,
                            100000,
                            32,
                            true
                          );
           $iv= random_bytes(12);
           $tag='';
           $encrypted=openssl_encrypt(
                                    $message,
                                    'aes-256-gcm',
                                    $key,
                                    OPENSSL_RAW_DATA,
                                    $iv,
                                    $tag                      
                                    );
           if($encrypted===false) break;
           $message=base64_encode($salt.$iv.$tag.$encrypted);
           
       }
       return $message;   
    }

}
