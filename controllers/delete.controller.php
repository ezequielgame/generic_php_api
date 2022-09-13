<?php


    require_once("models/delete.model.php");

    class DeleteController{


        static public function deleteData($table, $id, $nameId){

            $response = DeleteModel::deleteData($table, $id, $nameId);

            if(is_string($response)){
                echo $response;
                return;
            }

            self::responser($response);
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