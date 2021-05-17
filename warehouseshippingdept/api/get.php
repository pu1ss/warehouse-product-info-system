<?php
    require_once('product.php');
    require_once('db_utility.php');
    require_once('response_utility.php');

    header('Content-Type: application/json');

    get_db_connection(function ($connection) {
        $products = getAllProducts($connection);

        send_success_response($products);
    }, function($error){
        send_server_error_response("Failed to connect to database.");
    });
    
    
    