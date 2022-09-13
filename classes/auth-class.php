<?php

    // require_once("connection/connection.php");
    // require_once("response-class.php");

    // class Auth extends Connection{

    //     public function login($json){
    //         $_response = new Reponse();
    //         $data = json_decode($json,true);
    //         if(!isset($data["mail"]) || !isset($data["password"])){
    //             // Key error
    //             return $_response->error400();
    //         }else{
    //             // Ok
    //             $mail = $data["mail"];
    //             $password = $data["password"];
    //             $data = $this->getUserData($mail);
    //             if($data){
    //                 // Mail exists
    //                 if(password_verify($password,$data[0]["password"])){
                        
    //                 }else{
    //                     // Wrong password
    //                     return $_response->error200("Wrong password");
    //                 }
    //             }else{
    //                 // Mail not found in db
    //                 return $_response->error200("Mail not exists");
    //             }
    //         }
    //     }

    //     private function getUserData($mail){
    //         $query = "select id_user, password from users where mail = '$mail'";
    //         $data = parent::getData($query);
    //         if(isset($data[0]["id_user"])){
    //             return $data;
    //         }else{
    //             return 0;
    //         }
    //     }

    //     private function insertToken($user_id){
            
    //     }

    // }

?>