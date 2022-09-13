<?php

    // MODEL

    // Requirements
    require_once("connection/connection.php");

    class GetModel{

        private const COMODIN = ",";

        // Just Select
        static public function getData($table, $select,$orderBy,$orderMode,$page,$pageSize){

            //Validate table exists
            if(empty(Connection::getColumns($table)) || !Connection::validColumns($table,$select)){
                return null;
            }
            
            if($orderBy != null && $orderMode != null){
                $query = "select $select from $table order by $orderBy $orderMode";
            }else{
                $query = "select $select from $table";
            }

            if($page != null && $pageSize != null){
                $lowLimit = ($pageSize * $page - 1) - ($pageSize - 1);
                $query .= " limit $lowLimit, $pageSize";
            }


            $_connection = new Connection();

            $stmt = $_connection->connect()->prepare($query);

            try{
                $stmt->execute();
            }catch(PDOException $e){
                return  Response::statusResponse();
            }
            

            return $stmt->fetchAll(PDO::FETCH_CLASS);

        }

        // Get data from related tables
        static public function getRelationData($table, $select,$rel,$relType,$orderBy,$orderMode,$page,$pageSize){ 


            $relArray = explode(",",$rel); //Related tables names in plural categories, branches, etc...
            $relTypeArray = explode(",",$relType); //Related tables suffixes category, branch, etc...


            if(count($relArray) > 1){
                $innersText = "";
                //$relArray[0] = Main table categories, items, etc...
                //$relTypeArray[0] = Main table suffix category, item, etc...
                foreach ($relArray as $key => $value) {
                    //Validate table exists
                    if(empty(Connection::getColumns($value))){
                        return null;
                    }
                    // $key = index   =>   $value = related table name
                    // for each related table
                    // inner join RelatedTableName
                    // on MainTable.id_RelatedTableSuffix_MainTableSuffix
                    // = RelatedTableName.id_RelatedTableSuffix
                    if($key > 0){
                        $innersText .= " inner join ".$value
                                        ." on ".$relArray[0].".id_".$relTypeArray[$key]."_".$relTypeArray[0]
                                        ." = ".$value.".id_".$relTypeArray[$key]." ";
                    }
                }
            }else{
                return null;
            }

            if($orderBy != null && $orderMode != null){
                if(isset($innersText)){
                    $query = "select $select from $table $innersText order by $orderBy $orderMode";
                } else {
                    $query = "select $select from $table order by $orderBy $orderMode";
                }
                
            }else{
                if(isset($innersText)){
                    $query = "select $select from $table $innersText";
                } else{
                    $query = "select $select from $table";
                }
                
            }

            if($page != null && $pageSize != null){
                $lowLimit = ($pageSize * $page - 1) - ($pageSize - 1);
                $query .= " limit $lowLimit, $pageSize";
            }

            $_connection = new Connection();

            $stmt = $_connection->connect()->prepare($query);

            try{
                $stmt->execute();
            }catch(PDOException $e){
                return null;
            }
            

            return $stmt->fetchAll(PDO::FETCH_CLASS);

        }

        // Get data from related tables where...
        static public function getRelationDataWhere($table, $select,$linkTo,$equalTo,$rel,$relType,$orderBy,$orderMode,$page,$pageSize){ 
            
            //Wheres
            $linkToArray = explode(",",$linkTo);
            $equalToArray = explode(self::COMODIN,$equalTo);

            $linkToText = "";

            if(count($linkToArray)>1){
                foreach ($linkToArray as $key => $value) {
                    if($key > 0){
                        $linkToText .= "and ".$value." = :".$value." ";
                    }
                }
            }

            //Relations
            $relArray = explode(",",$rel); //Related tables names in plural categories, branches, etc...
            $relTypeArray = explode(",",$relType); //Related tables suffixes category, branch, etc...


            if(count($relArray) > 1){
                $innersText = "";
                //$relArray[0] = Main table categories, items, etc...
                //$relTypeArray[0] = Main table suffix category, item, etc...
                foreach ($relArray as $key => $value) {
                    //Validate table exists
                    if(empty(Connection::getColumns($value))){
                        return null;
                    }
                    // $key = index   =>   $value = related table name
                    // for each related table
                    // inner join RelatedTableName
                    // on MainTable.id_RelatedTableSuffix_MainTableSuffix
                    // = RelatedTableName.id_RelatedTableSuffix
                    if($key > 0){
                        $innersText .= " inner join ".$value
                                        ." on ".$relArray[0].".id_".$relTypeArray[$key]."_".$relTypeArray[0]
                                        ." = ".$value.".id_".$relTypeArray[$key]." ";
                    }
                }
            }else{
                return null;
            }

            if($orderBy != null && $orderMode != null){
                if(isset($innersText)){
                    $query = "select $select from $table $innersText where $linkToArray[0] = :$linkToArray[0] $linkToText  order by $orderBy $orderMode";
                } else {
                    $query = "select $select from $table where $linkToArray[0] = :$linkToArray[0] $linkToText order by $orderBy $orderMode";
                }
                
            }else{
                if(isset($innersText)){
                    $query = "select $select from $table $innersText where $linkToArray[0] = :$linkToArray[0] $linkToText ";
                } else{
                    $query = "select $select from $table where $linkToArray[0] = :$linkToArray[0] $linkToText";
                }
                
            }

            if($page != null && $pageSize != null){
                $lowLimit = ($pageSize * $page - 1) - ($pageSize - 1);
                $query .= " limit $lowLimit, $pageSize";
            }

            $_connection = new Connection();

            $stmt = $_connection->connect()->prepare($query);

            foreach ($linkToArray as $key => $value) {
                $stmt->bindParam(":".$value, $equalToArray[$key], PDO::PARAM_STR);
            }

            try{
                $stmt->execute();
            }catch(PDOException $e){
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_CLASS);


        }

        // Where Clause
        static public function getDataWhere($table, $select,$linkTo,$equalTo,$orderBy,$orderMode,$page,$pageSize){

            //Validate table exists
            if(empty(Connection::getColumns($table)) || !Connection::validColumns($table,$select.",".$linkTo)){
                return null;
            }

            $linkToArray = explode(",",$linkTo);
            $equalToArray = explode(self::COMODIN,$equalTo);

            $linkToText = "";

            if(count($linkToArray)>1){
                foreach ($linkToArray as $key => $value) {
                    if($key > 0){
                        $linkToText .= "and ".$value." = :".$value." ";
                    }
                }
            }
            if($orderBy != null && $orderMode != null){
                $query = "select $select from $table where $linkToArray[0] = :$linkToArray[0] $linkToText order by $orderBy $orderMode";
            }else{
                $query = "select $select from $table where $linkToArray[0] = :$linkToArray[0] $linkToText";
            }

            if($page != null && $pageSize != null){
                $lowLimit = ($pageSize * $page - 1) - ($pageSize - 1);
                $query .= " limit $lowLimit, $pageSize";
            }

            $_connection = new Connection();
            $stmt = $_connection->connect()->prepare($query);
            
            foreach ($linkToArray as $key => $value) {
                $stmt->bindParam(":".$value, $equalToArray[$key], PDO::PARAM_STR);
            }

            try{
                $stmt->execute();
            }catch(PDOException $e){
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        }

        static public function getDataLike($table,$select,$linkTo,$search,$orderBy,$orderMode,$page,$pageSize){

            if(empty(Connection::getColumns($table)) || !Connection::validColumns($table,$select)){
                return null;
            }

            $query = "select $select from $table";

            $linkToArray = explode(",",$linkTo);
            $searchToArray = explode(self::COMODIN,$search);

            $linkToText = "";

            if(count($linkToArray)>1){
                foreach ($linkToArray as $key => $value) {
                    if($key > 0){
                        $linkToText .= "and ".$value." = :".$value." ";
                    }
                }
            }

            $query .= " where $linkToArray[0] like '%$searchToArray[0]%' $linkToText";

            if($orderBy != null && $orderMode != null){
                $query .= " order by $orderBy $orderMode";
            }

            if($page != null && $pageSize != null){
                $lowLimit = ($pageSize * $page - 1) - ($pageSize - 1);
                $query .= " limit $lowLimit, $pageSize";
            }
            
            $_connection = new Connection();
            $stmt = $_connection->connect()->prepare($query);

            foreach ($linkToArray as $key => $value) {
                if($key > 0){
                    $stmt->bindParam(":".$value, $searchToArray[$key], PDO::PARAM_STR);
                }
                
            }

            try{
                $stmt->execute();
            }catch(PDOException $e){
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        }

        static public function getRelationDataLike($table, $select,$linkTo,$search,$rel,$relType,$orderBy,$orderMode,$page,$pageSize){ 
            
            //Wheres
            $query = "select $select from $table";

            $linkToArray = explode(",",$linkTo);
            $searchToArray = explode(self::COMODIN,$search);

            $linkToText = "";

            if(count($linkToArray)>1){
                foreach ($linkToArray as $key => $value) {
                    if($key > 0){
                        $linkToText .= "and ".$value." = :".$value." ";
                    }
                }
            }

            //Relations
            $relArray = explode(",",$rel); //Related tables names in plural categories, branches, etc...
            $relTypeArray = explode(",",$relType); //Related tables suffixes category, branch, etc...


            if(count($relArray) > 1){
                $innersText = "";
                //$relArray[0] = Main table categories, items, etc...
                //$relTypeArray[0] = Main table suffix category, item, etc...
                foreach ($relArray as $key => $value) {
                    //Validate table exists
                    if(empty(Connection::getColumns($value))){
                        return null;
                    }
                    // $key = index   =>   $value = related table name
                    // for each related table
                    // inner join RelatedTableName
                    // on MainTable.id_RelatedTableSuffix_MainTableSuffix
                    // = RelatedTableName.id_RelatedTableSuffix
                    if($key > 0){
                        $innersText .= " inner join ".$value
                                        ." on ".$relArray[0].".id_".$relTypeArray[$key]."_".$relTypeArray[0]
                                        ." = ".$value.".id_".$relTypeArray[$key]." ";
                    }
                }
            }else{
                return null;
            }

            if($orderBy != null && $orderMode != null){
                if(isset($innersText)){
                    $query .= " $innersText where $linkToArray[0] like '%$searchToArray[0]%' $linkToText  order by $orderBy $orderMode";
                } else {
                    $query .= " where where $linkToArray[0] like '%$searchToArray[0]%' $linkToText order by $orderBy $orderMode";
                }
                
            }else{
                if(isset($innersText)){
                    $query .= " $innersText where $linkToArray[0] like '%$searchToArray[0]%' $linkToText ";
                } else{
                    $query .= " where $linkToArray[0] like '%$searchToArray[0]%' $linkToText";
                }
                
            }

            if($page != null && $pageSize != null){
                $lowLimit = ($pageSize * $page - 1) - ($pageSize - 1);
                $query .= " limit $lowLimit, $pageSize";
            }

            $_connection = new Connection();

            $stmt = $_connection->connect()->prepare($query);


            foreach ($linkToArray as $key => $value) {
                if($key > 0){
                    $stmt->bindParam(":".$value, $searchToArray[$key], PDO::PARAM_STR);
                }
                
            }

            try{
                $stmt->execute();
            }catch(PDOException $e){
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_CLASS);


        }

        // Between
        static public function getDataRange($table,$select,$linkTo,$between1,$between2,$orderBy,$orderMode,$page,$pageSize,$filterTo,$filterIn){

            // $linkToArray = explode(",",$linkTo);
            // $equalToArray = explode(self::COMODIN,$equalTo);

            // $linkToText = "";

            // if(count($linkToArray)>1){
            //     foreach ($linkToArray as $key => $value) {
            //         if($key > 0){
            //             $linkToText .= "and ".$value." = :".$value." ";
            //         }
            //     }
            // }

            if(empty(Connection::getColumns($table)) || !Connection::validColumns($table,$select)){
                return null;
            }

            $query = "select $select from $table where $linkTo between '$between1' and '$between2'";

            if($filterIn != null && $filterTo != null){
                $filterInArray = explode(",",$filterIn);
                if(count($filterInArray) > 1){
                    $filterInText = "";
                    foreach ($filterInArray as $key => $value) {
                        $filterInText .= "'".$value;
                        if($key != count($filterInArray)-1){
                            $filterInText .= "',";
                        }else{
                            $filterInText .= "'";
                        }
                    }
                    $query .= " and ".$filterTo." in (".$filterInText.")";
                }else{
                    $query .= " and ".$filterTo." in ('".$filterIn."')";
                }
            }

            if($orderBy != null && $orderMode != null){
                $query .= " order by $orderBy $orderMode";
            }

            if($page != null && $pageSize != null){
                $lowLimit = ($pageSize * $page - 1) - ($pageSize - 1);
                $query .= " limit $lowLimit, $pageSize";
            }

            $_connection = new Connection();
            $stmt = $_connection->connect()->prepare($query);
            
            // foreach ($linkToArray as $key => $value) {
            //     $stmt->bindParam(":".$value, $equalToArray[$key], PDO::PARAM_STR);
            // }

            try{
                $stmt->execute();
            }catch(PDOException $e){
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        }

        // Between and related tables
        static public function getRelDataRange($table,$select,$rel,$relType,$linkTo,$between1,$between2,$orderBy,$orderMode,$page,$pageSize,$filterTo,$filterIn){

            // $linkToArray = explode(",",$linkTo);
            // $equalToArray = explode(self::COMODIN,$equalTo);

            // $linkToText = "";

            // if(count($linkToArray)>1){
            //     foreach ($linkToArray as $key => $value) {
            //         if($key > 0){
            //             $linkToText .= "and ".$value." = :".$value." ";
            //         }
            //     }
            // }

            $query = "select $select from $table";

            $relArray = explode(",",$rel); //Related tables names in plural categories, branches, etc...
            $relTypeArray = explode(",",$relType); //Related tables suffixes category, branch, etc...


            if(count($relArray) > 1){
                $innersText = "";
                //$relArray[0] = Main table categories, items, etc...
                //$relTypeArray[0] = Main table suffix category, item, etc...
                foreach ($relArray as $key => $value) {
                    //Validate table exists
                    if(empty(Connection::getColumns($value))){
                        return null;
                    }
                    // $key = index   =>   $value = related table name
                    // for each related table
                    // inner join RelatedTableName
                    // on MainTable.id_RelatedTableSuffix_MainTableSuffix
                    // = RelatedTableName.id_RelatedTableSuffix
                    if($key > 0){
                        $innersText .= " inner join ".$value
                                        ." on ".$relArray[0].".id_".$relTypeArray[$key]."_".$relTypeArray[0]
                                        ." = ".$value.".id_".$relTypeArray[$key]." ";
                    }
                }
            }else{
                return null;
            }
            
            if(isset($innersText)){
                $query .= " $innersText";
            }

            $query .= " where $linkTo between '$between1' and '$between2'";
            if($filterIn != null && $filterTo != null){
                $filterInArray = explode(",",$filterIn);
                if(count($filterInArray) > 1){
                    $filterInText = "";
                    foreach ($filterInArray as $key => $value) {
                        $filterInText .= "'".$value;
                        if($key != count($filterInArray)-1){
                            $filterInText .= "',";
                        }else{
                            $filterInText .= "'";
                        }
                    }
                    $query .= " and ".$filterTo." in (".$filterInText.")";
                }else{
                    $query .= " and ".$filterTo." in ('".$filterIn."')";
                }
            }

            if($orderBy != null && $orderMode != null){
                $query .= " order by $orderBy $orderMode";
            }

            if($page != null && $pageSize != null){
                $lowLimit = ($pageSize * $page - 1) - ($pageSize - 1);
                $query .= " limit $lowLimit, $pageSize";
            }

            $_connection = new Connection();
            $stmt = $_connection->connect()->prepare($query);
            
            // foreach ($linkToArray as $key => $value) {
            //     $stmt->bindParam(":".$value, $equalToArray[$key], PDO::PARAM_STR);
            // }

            try{
                $stmt->execute();
            }catch(PDOException $e){
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        }

    }
