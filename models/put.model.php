<?php

    require_once("models/connection/connection.php");
    require_once("classes/response.class.php");
    require_once("get.model.php");

    class PutModel{


        static public function putData($table, $data, $id, $nameId){

            // Valid id
            $valid = GetModel::getDataWhere($table, $nameId, $nameId,$id,null,null,null,null);
            
            if(empty($valid)){

                return array(
                    "msg"=>"Invalid ID",
                );

            }

            $set = "";
            foreach ($data as $key => $value) {
                $set .= $key." = :".$key.",";
            }

            $set = substr($set,0,-1);

            $query = "update $table set $set where $nameId = :$nameId";

            $conn = Connection::connect();

            $stmt = $conn->prepare($query);


            foreach ($data as $key => $value) {
                $stmt->bindParam(":".$key,$data[$key],PDO::PARAM_STR);
            }
            $stmt->bindParam(":".$nameId,$id,PDO::PARAM_STR);
        

            try {
                if($stmt->execute()){
                    return array(
                        "msg"=>"Updated",
                    );
                }
            } catch (PDOException $e) {

                if($e->getCode()==23000){
                    return Response::error409();
                } else {
                    return array(
                        "msg"=>$e
                    );
                }
            }
           

        }


    }

?>