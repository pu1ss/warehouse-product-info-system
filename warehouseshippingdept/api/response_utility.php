<?php
    define("SUCCESS_CODE", 200);
    define("CLIENT_ERROR_CODE", 400);
    define("SERVER_ERROR_CODE", 500);

    function send_success_response($data) {
        header('Content-Type: application/json');
        http_response_code(SUCCESS_CODE);
        echo json_encode(array('data' => $data));
    }

    function send_client_error_response($message) {
        header('Content-Type: application/json');
        http_response_code(CLIENT_ERROR_CODE);
        echo json_encode(array('message' => $message));
    }

    function send_server_error_response($message) {
        header('Content-Type: application/json');
        http_response_code(SERVER_ERROR_CODE);
        echo json_encode(array('message' => $message));
    }

