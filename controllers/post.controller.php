<?php


    require_once("models/post.model.php");
    require_once("models/get.model.php");
    require_once("models/put.model.php");
    require_once("models/connection/connection.php");
    require_once("vendor/autoload.php");
    use Firebase\JWT\JWT;

    class PostController{


        static public function postData($table, $data, $print = true){

            $response = PostModel::postData($table, $data);

            if($print){
                if(is_string($response)){
                    echo $response;
                    return;
                }
    
                self::responser($response);
            }
            
        }

        //Registers
        static public function postRegister($table, $data, $suffix){
            if(isset($data["password_".$suffix]) && $data["password_".$suffix] != null){
                $pass = $data["password_".$suffix];
                $crypt = password_hash($pass, PASSWORD_DEFAULT);
                $data["password_".$suffix] = $crypt;

                $validEmail = GetModel::getDataWhere($table, "*", "email_".$suffix, $data["email_".$suffix],null,null,null,null);
                
                if(empty($validEmail)){
                    $response = PostModel::postData($table, $data);

                    if(is_string($response)){
                        echo $response;
                        return;
                    }

                    if($suffix == "user"){
                        // $rolesNames = ["Dueño", "Administrador","Empleado"];
                        // $rolesDescriptions = [
                        //     "Acceso a todo el sistema",
                        //     "Modificar productos, categorias; hacer compras, ventas",
                        //     "Hacer ventas, consultar productos y categorias"
                        // ];
                        // for($i = 0; $i < count($rolesNames); $i++){
                        //     $rolesData = array();
                        //     $rolesData["id_user_role"] = $response["lastId"];
                        //     $rolesData["name_role"] = $rolesNames[$i];
                        //     $rolesData["description_role"] = $rolesDescriptions[$i];
                        //     $roleResponse = PostModel::postData("roles",$rolesData);
                        //     if($i == 0){ // Assign owner role
                        //         PutModel::putData("users",["id_role_user"=>$roleResponse["lastId"]],$response["lastId"],"id_user");
                        //     }
                        // }
                        self::postData("codes",["id_user_code"=>$response["lastId"]],false);
                    }
                    

                    self::responser($response);
                } else {
                    echo Response::error409();
                }
                
            }

        }

        //Logins

        static public function postLogin($table, $data, $suffix){

            // Valid user
            $valid = GetModel::getDataWhere($table, "*", "email_".$suffix, $data["email_".$suffix],null,null,null,null);
            

            if(empty($valid)){

                echo Response::error401();
                return;

            }

            if(isset($data["password_".$suffix]) && $data["password_".$suffix] != null){
                $password = $data["password_".$suffix];
                if(!password_verify($password,$valid[0]->{"password_".$suffix})){
                    echo Response::error401();
                    return;
                }
                $currentToken = $valid[0]->{"token_".$suffix};

                if(Connection::validToken("Bearer ".$currentToken,$table,$suffix)){
                    $response = array(
                        $suffix=>$valid[0]
                    );
                }else{
                    $jwt = Connection::jwt($valid[0]->{"id_".$suffix}, $valid[0]->{"email_".$suffix});
                    $token = JWT::encode($jwt,
                    "rhQsrN2v94V18xWUCgdfhVhM4FyyN6hcNeixwkZ3IpwVBPI3hUe8KqgEMPk4h5SRM6cUoLImyCjxZAmdOcMC9AzMhEFkWJgX99FSYxw86XzhSx5EEVSqwaya4dKHgUNK",
                    "HS256");
                    //Save token
                    $auth = array(
                        "token_".$suffix => $token,
                        "token_exp_".$suffix => $jwt["exp"]
                    );
                    $update = PutModel::putData($table,$auth,$valid[0]->{"id_".$suffix},"id_".$suffix);
                    if(isset($update["msg"]) && $update["msg"] == "Updated"){
                        if(isset($valid[0]->{"password_".$suffix})){
                            unset($valid[0]->{"password_".$suffix});
                        }
                        $valid[0]->{"token_".$suffix} = $token;
                        $valid[0]->{"token_exp_".$suffix} = $jwt["exp"];
                        $response = array(
                            $suffix=>$valid[0]
                        );
                    }
                }
                echo(
                    json_encode($response)
                );
            }
        }

        //Authorize token
        static public function postAuthToken($table, $token, $suffix){
            // In data: token_suffix

            $token = str_replace("Bearer ","",$token);
            //Received valid token
            $info = GetModel::getDataWhere($table, "id_".$suffix, "token_".$suffix, $token,null,null,null,null);
            
            $idTokenPerson = $info[0] -> {"id_".$suffix};
        
            echo Response::statusResponse(array(
                "status" => 200,
                "total" => 1,
                $suffix => $idTokenPerson
            ));

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