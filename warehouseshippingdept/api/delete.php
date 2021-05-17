<?php
    require_once('product.php');
    require_once('db_utility.php');
    require_once('response_utility.php');

    get_db_connection(function ($connection) {
        $product_id = $_GET["product_id"];

        if($product_id === NULL) {
            send_client_error_response("Product ID is NULL");
            return;
        }

        $product_deleted = delete_product($connection, $product_id);
        if($product_deleted === false){
            send_client_error_response("Deletion Failed for ID = $product_id");
            return;
        }

        send_success_response($product_id);
    }, function($error){
        send_server_error_response("Failed to connect to database.");
    });

