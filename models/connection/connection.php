<?php

    require_once("models/get.model.php");

    
    

    class Connection{
        const SECOND = 1;
        const MINUTE = self::SECOND*60;
        const HOUR  = self::MINUTE*60;
        const DAY = self::HOUR*24;


        public static function connectionData(){
            $dir = dirname(__FILE__);
            $configFile = "config";
            $configs = json_decode(file_get_contents($dir."/".$configFile),true);
            $connectionConfig = $configs["connection"];
            return $connectionConfig; //Array
        }

        public static function connect(){
            $dataList = self::connectionData();
            try{
                $connection = new PDO(
                    "mysql:host=".$dataList["server"].";dbname=".$dataList["database"],
                    $dataList["user"],
                    $dataList["password"]
                );
                $connection->exec("set names utf8");
            }catch(PDOException $e){
                die("Error: ".$e->getMessage());
            }
            return $connection;
        }

        //Get columns from tables
        static public function getColumns($table){

            $database = self::connectionData()["database"];

            return self::connect()
                ->query("select COLUMN_NAME as item from information_schema.columns where table_schema= '$database' and table_name = '$table'")
                ->fetchAll(PDO::FETCH_OBJ);

        }

        //Validate columns
        static public function validColumns($table, $select){
            $selectArray = explode(",",$select);
            if($selectArray[0] != "*"){
                $selectArray = array_unique($selectArray);
            }else{
                array_shift($selectArray);
            }
            $columns = self::getColumns($table);
            $sum = 0;
            foreach ($columns as $key => $value) {
                if(in_array($value->item,$selectArray)){
                    $sum += 1;
                }
            }
            return $sum == count($selectArray);
        }


        //Generate auth token
        static public function jwt($id, $email){

            $time = time();

            $token = array(
                "iat" => $time,//Start
                "exp" => $time + self::DAY, // Expiration time
                "data" => [
                    "id" => $id,
                    "email" => $email
                ]
            );

            return $token;
            
        }

        //Validate auth token
        static public function validToken($token, $domain, $suffix){

            $token = str_replace("Bearer ","",$token);

            // Get token user
            $user = GetModel::getDataWhere($domain, "token_exp_".$suffix, "token_".$suffix, $token,null,null,null,null);
 
            $time = time();
            if(!empty($user)){
                $expiration = $user[0]->{"token_exp_".$suffix};
            }
            
            return !empty($user) and $expiration > $time;


        }

    }

?>