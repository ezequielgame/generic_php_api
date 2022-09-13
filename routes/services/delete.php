<?php


require_once("models/connection/connection.php");
require_once("controllers/delete.controller.php");

if(isset($_GET["id"]) && isset($_GET["nameId"])){


    // Get data from form
    $data = array();
    parse_str(file_get_contents('php://input'),$data); //String
    // print_r($data); // to array

    $columns = "";

    $columns .= $_GET["nameId"];

    // $columns = substr($columns,0,-1);

    if(Connection::validColumns($table, $columns)){

        $headers = getallheaders();
            

        if(isset($headers["authorization"])){
            $token = $headers["authorization"];
            //Validate token
            $domain = $_GET["domain"] ?? "users";
            $suffix = $_GET["suffix"] ?? "user";

            $validate = Connection::validToken($token, $domain, $suffix);
            
            if(!$validate){
                $validate = Connection::validToken($token, "employees", "employee");
                if(!$validate){
                    echo Response::error401();
                }else{
                    //Controller response
                    $response = new DeleteController();
                    $response->deleteData($table, $_GET["id"],$_GET["nameId"]);
                }
            }else {
                //Controller response
                $response = new DeleteController();
                $response->deleteData($table, $_GET["id"],$_GET["nameId"]);
            }

        }else{
            echo Response::error401();
        }
        

    }else{
        echo Response::statusResponse(array(
            "status" => 403,
            "result" => [
                "errorMsg" => "Bad columns"
            ]
            ));
    }

}


?>