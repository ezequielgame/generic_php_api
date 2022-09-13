<?php

    require_once("models/connection/connection.php");
    require_once("controllers/post.controller.php");
    require_once("controllers/put.controller.php");
    require_once("controllers/get.controller.php");

    if(isset($_POST)){

        $response = new PostController();

        if(isset($_GET["action"]) && $_GET["action"] == "authToken"){
            $suffix = $_GET["suffix"] ?? "user";
            $headers = getallheaders();
            if(isset($headers["authorization"])){
                $token = $headers["authorization"];
                if(Connection::validToken($token, $table, $suffix)){
                    
                    $response->postAuthToken($table, $token, $suffix);

                } else {
                    echo Response::error401();
                }
            }else{
                echo Response::error401();
            }
            return;
        }

        if(isset($_GET['action']) && !in_array($_GET['action'],["login","register","weblogin"])){

            if(isset($_GET["nameId"]) && isset($_GET["id"])){
                $nameId = $_GET["nameId"];
                $id = $_GET["id"];
            }else{
                echo Response::error400();
            }

            if($_GET['action'] == "upload"){

                if(isset($_FILES['image']['name'])){
                    $target_dir = "uploads/";
    
                    $target_file = $target_dir."profile_".$nameId."_".$id.".".pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    

                    $suffix = "";
                    if($table == "users"){
                        $suffix = "user";
                    } else if($table == "employees"){
                        $suffix = "employee";
                    } else if($table == "items"){
                        $suffix = "item";
                    } else if($table == "branches"){
                        $suffix = "branch";
                    } 
    
                    if(move_uploaded_file($_FILES['image']['tmp_name'],$target_file)){
                        $data = array();
                        $data["img_path_".$suffix] = $target_file;
                        
                        PutController::putData($table, $data, $id, $nameId);    
                    } else {
                        echo Response::error400();
                    }
                    return;
    
                }
                else{
                    echo Response::error400();
                    return;
                }

            }
        
        }



        $postColumns = "";
        $actions = array(
            "register", "login", "action" ,"suffix"
        );
        foreach ($_POST as $key => $value) {
            if(!in_array($key,$actions)){
                $postColumns .= $key.",";
            }
        }
        
        $postColumns = substr($postColumns,0,-1);

        if(Connection::validColumns($table, $postColumns)){

            //Registers
            if(isset($_GET["action"]) && $_GET["action"] == "register"){

                $suffix = $_POST["suffix"] ?? "user";

                $postArray = $_POST;
                unset($postArray["suffix"]);

                //Controller response
                $response->postRegister($table, $postArray, $suffix);

            } else if(isset($_GET["action"]) && $_GET["action"] == "login"){
                $suffix = $_POST["suffix"] ?? "user";
                
                $postArray = $_POST;
                unset($postArray["suffix"]);

                //Controller response
                $response->postLogin($table, $postArray, $suffix);

            } else{

                $headers = getallheaders();
            

                if(isset($headers["authorization"])){
                    $token = $headers["authorization"];

                    //Validate token
                    $domain = $_GET["domain"] ?? "users";
                    $suffix = $_GET["suffix"] ?? "user";

                    $validate = Connection::validToken($token, $domain, $suffix);
                    
                    if(!$validate){
                        //Post
                        $validate = Connection::validToken($token, "employees", "employee");
                        if(!$validate){
                            echo Response::error401();
                        }else {
                            $response->postData($table,$_POST);
                        }
                    }else {
                        $response->postData($table,$_POST);
                    }

                }else{
                    echo Response::error401();
                }

                
            }

        }else{
            echo Response::statusResponse(array(
                "status" => 400,
                "result" => [
                    "errorMsg" => "Bad columns"
                ]
                ));
        }
    }

?>