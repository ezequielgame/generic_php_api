<?php

    // Display errors

    ini_set("display_errors",1);
    ini_set("log_errors",1);
    ini_set("error_log","C:/xampp/htdocs/tiendaris/api/php_error_log");

    // Requirements
    require_once("controllers/route.controller.php");
    
    $index = new RoutesController();
    $index->index();

?>