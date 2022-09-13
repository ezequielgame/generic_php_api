<?php


    //Requirements
    require_once("controllers/get.controller.php");

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
                return;
            }
        }
    } else {
        echo Response::error401();
        return;
    }
    
    $select = $_GET["select"] ?? "*"; // Default *

    $orderBy = $_GET["orderBy"] ?? null;
    $orderMode = $_GET["orderMode"] ?? "asc";

    $page = $_GET["page"] ?? null;
    $pageSize = $_GET["pageSize"] ?? null;

    $filterTo = $_GET["filterTo"] ?? null;
    $filterIn = $_GET["filterIn"] ?? null;
     
    $response = new GetController();
    // Where Clause
    if(isset($_GET["linkTo"]) && isset($_GET["equalTo"])){
        $linkTo = $_GET["linkTo"];
        $equalTo = $_GET["equalTo"];
        if(isset($_GET["rel"]) && isset($_GET["relType"])){
            $rel = $_GET["rel"];
            $relType = $_GET["relType"];
            $response->getRelationDataWhere($table, $select,$linkTo,$equalTo,$rel,$relType,$orderBy,$orderMode,$page,$pageSize); //Table relation with where
        } else{
            $response->getDataWhere($table,$select,$linkTo,$equalTo,$orderBy,$orderMode,$page,$pageSize);
        }
    }else if (isset($_GET["linkTo"]) && isset($_GET["search"]) ){ // Search %like%
        $search = $_GET["search"];
        $linkTo = $_GET["linkTo"];
        if(isset($_GET["rel"]) && isset($_GET["relType"])){
            $rel = $_GET["rel"];
            $relType = $_GET["relType"];
            $response->getRelationDataLike($table, $select,$linkTo,$search,$rel,$relType,$orderBy,$orderMode,$page,$pageSize); 
        }else{
            $response->getDataLike($table,$select,$linkTo,$search,$orderBy,$orderMode,$page,$pageSize);
        }
        
    } else if (isset($_GET["linkTo"]) && isset($_GET["between1"]) && isset($_GET["between2"])){ // Search %like%
        $linkTo = $_GET["linkTo"];
        $between1 = $_GET["between1"];
        $between2 = $_GET["between2"];
        if(isset($_GET["rel"]) && isset($_GET["relType"])){
            $rel = $_GET["rel"];
            $relType = $_GET["relType"];
            $response->getRelDataRange($table,$select,$rel,$relType,$linkTo,$between1,$between2,$orderBy,$orderMode,$page,$pageSize,$filterTo,$filterIn);
        }else{
            $response->getDataRange($table,$select,$linkTo,$between1,$between2,$orderBy,$orderMode,$page,$pageSize,$filterTo,$filterIn);
        }
    } else if(isset($_GET["rel"]) && isset($_GET["relType"])){ // Tables relations whitout where
        $rel = $_GET["rel"];
        $relType = $_GET["relType"];
        $response->getRelationData($table, $select,$rel,$relType,$orderBy,$orderMode,$page,$pageSize);
    } else{ // Select
        $response->getData($table, $select,$orderBy,$orderMode,$page,$pageSize);
    }
    
?>