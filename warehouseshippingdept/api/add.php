<?php
    require_once('product.php');
    require_once('db_utility.php');
    require_once('response_utility.php');

    get_db_connection(function ($connection) {
        $product_name = $_POST['product_name'];
        if(!isset($product_name) || $product_name == "") {
            send_client_error_response("Product Name is Empty. Please fill in information and resubmit.");
            return;
        }

        $product_weight = $_POST['product_weight'];
        if(!isset($product_weight) || $product_weight == "") {
            send_client_error_response("Product Weight is Empty. Please fill in information and resubmit.");
            return;
        }

        $product_length = $_POST['product_length'];
        if(!isset($product_length) || $product_length == "") {
            send_client_error_response("Product Length is Empty. Please fill in information and resubmit.");
            return;
        }

        $product_width = $_POST['product_width'];
        if(!isset($product_width) || $product_width == "") {
            send_client_error_response("Product Width is Empty. Please fill in information and resubmit.");
            return;
        }

        $product_height = $_POST['product_height'];
        if(!isset($product_height) || $product_height == "") {
            send_client_error_response("Product Height is Empty. Please fill in information and resubmit.");
            return;
        }

        $product = new Product($product_name, $product_weight,  $product_width, $product_length, $product_height);

        $inserted_product = insert_product($connection, $product);
        if($inserted_product === NULL){
            send_client_error_response("A product with the name '$product_name' already exists. Please choose a new name.");
            return;
        }

        send_success_response($inserted_product);
    }, function($error){
        send_server_error_response("Failed to connect to database.");
    });

