<?php
    require_once('db_config.php');

    function get_db_connection($on_success, $on_error){
        $connection = new mysqli(SERVER_NAME, USER_NAME, PASSWORD);
        if ($connection->connect_error) {
            return $on_error("Connection failed: " . $connection->connect_error);
        }

        $create_database_sql = "CREATE DATABASE IF NOT EXISTS " . DATABASE_NAME;
        if ($connection->query($create_database_sql) !== true) {
            return $on_error("Error creating database \'" . DATABASE_NAME . "\': " . $connection->connect_error);
        }

        $use_table_sql = "USE " . DATABASE_NAME;
        if ($connection->query($use_table_sql) !== true) {
            return $on_error("Error using table \'" . TABLE_NAME . "\': " . $connection->error);
        }
;
        if (!productsTableExists($connection)) {
            $create_table_sql = "CREATE TABLE IF NOT EXISTS " . TABLE_NAME . " (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) UNIQUE NOT NULL,
                weight_kg FLOAT,
                length_cm FLOAT,
                width_cm FLOAT,
                height_cm FLOAT
            )";

            if ($connection->query($create_table_sql) !== true) {
                return $on_error("Error creating table \'" . TABLE_NAME . "\': " . $connection->error);
            }

            initialize_products_table($connection);
        }

        try {
            $on_success($connection);
        }
        finally{  
            if($connection !== NULL) {
                $connection -> close();
            }
        }
    }

    function productsTableExists($connection){
        if(invalid_connection($connection)){
            return false;
        }

        $db_name = DATABASE_NAME;
        $table_name = TABLE_NAME;

        $products_table_exists_sql = "SELECT EXISTS (
            SELECT * FROM information_schema.tables 
            WHERE table_schema = '$db_name' AND table_name = '$table_name'
        ) AS has_table";
        
        $result = $connection->query( $products_table_exists_sql);
        $data = $result->fetch_assoc();

        if($data["has_table"] == 1) {
            // echo "\n\ntrue = " . $data["has_table"] . "\n\n";
            return true;
        }
 
        // echo "\n\nfalse\n\n";
        return false;
    }

    function initialize_products_table($connection){
        if(invalid_connection($connection)){
            return false;
        }

        $default_products = [ 
            new Product('Fiddle', 1, 60, 20, 10), 
            new Product('Dish', 0.1, 30, 30, 5),
            new Product('Spoon', 0.05, 15, 5, 2)
        ];

        foreach($default_products as $product) {
            insert_product($connection, $product);
        }
    }

    function getAllProducts($connection){
        if(invalid_connection($connection)){
            return NULL;
        }

        $table_name = TABLE_NAME;

        $sql = "SELECT * FROM $table_name ORDER BY name ASC";
        $result = $connection->query($sql);

        $products = [];
        if ($result !== false && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $product = new Product(
                    $row["name"],
                    $row["weight_kg"],
                    $row["length_cm"],
                    $row["width_cm"],
                    $row["height_cm"]
                );

                $product -> id = $row["id"];

                array_push($products, $product);
            }
        }

        return $products;
    }

    function insert_product($connection, $product){
        if(invalid_connection($connection)){
            return NULL;
        }

        if($product === NULL){
            return NULL;
        }

        $table_name = TABLE_NAME;

        $insert_product_sql = "INSERT INTO $table_name (name, weight_kg, length_cm, width_cm, height_cm) 
            VALUES ('$product->name', '$product->weight_kg', '$product->length_cm', '$product->width_cm', '$product->height_cm')";

        if ($connection->query($insert_product_sql) === true) {
            $inserted_id = $connection->insert_id;
            $product->id = $inserted_id;
            return $product;
        } 
        else {
            return NULL;
        }
    }

    function update_product($connection, $product){
        if(invalid_connection($connection)){
            return NULL;
        }

        if($product === NULL){
            return NULL;
        }

        $table_name = TABLE_NAME;

        $update_product_sql = "UPDATE $table_name SET name = '$product->name', weight_kg = $product->weight_kg, length_cm = $product->length_cm, width_cm = $product->width_cm, height_cm = $product->height_cm WHERE id = $product->id";
        if ($connection->query($update_product_sql) === true) {
            return $product;
        }
        else {
            return NULL;
        }
    }

    function delete_product($connection, $product_id){
        if(invalid_connection($connection)){
            return NULL;
        }

        if($product_id === NULL){
            return NULL;
        }

        $table_name = TABLE_NAME;

        $delete_product_sql = "DELETE FROM $table_name WHERE id = $product_id";

        if ($connection->query($delete_product_sql) === true) {
            return true;
        } 
        else {
            return false;
        }
    }

    function invalid_connection($connection) {
        if($connection === NULL){
            return true;
        }

        if ($connection->connect_error) {
            return true;
        }

        return false;
    }
