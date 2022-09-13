<?php


    require_once("models/put.model.php");

    class PutController{


        static public function putData($table, $data, $id, $nameId){

            if(isset($data["password_employee"]) && $data["password_employee"] != null){
                $pass = $data["password_employee"];
                $crypt = password_hash($pass, PASSWORD_DEFAULT);
                $data["password_employee"] = $crypt;
            }
            
            $response = PutModel::putData($table, $data, $id, $nameId);

            if(is_string($response)){
                echo $response;
                return;
            }

            if(isset($response) && $response != null){
                self::responser($response);
            } else { //Controller already response
                return;
            }
            
        }

        // Controller responses
        static public function responser($response){
            if(empty($response)){
                $jsonResponse = Response::error404();
            }else{
                $jsonResponse = Response::statusResponse(array(
                    "status" => 200,
                    "total" => count($response),
                    "result" => $response
                ));
            }
            echo($jsonResponse);
        }


    }


?>