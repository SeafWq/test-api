<?php

header('Content-type: application/json');
require 'connect.php';
require 'functions.php';


$request_method = $_SERVER["REQUEST_METHOD"];
print_r(json_encode($request_method));

$q = $_GET['q'];
$params = explode('/', $q);

$type = $params[0];
$id = $params[1];


switch ($request_method){
    case 'GET':
        if ($type === 'users') {
            if (isset($id)) {
                getUser($db_connect, $id);
            }else{
                getUsers($db_connect);
            }
        }
        break;
    case 'POST':
        if ($type === 'users') {
            addUser($db_connect, $_POST);
        }
        break;
    case  'PATCH':
        if ($type === 'users') {
            if (isset($id)) {
                $data = file_get_contents('php://input');
                $data = json_decode($data, true);
                updateUser($db_connect, $id, $data);
            }
        }
        break;
    case 'DELETE':
        if ($type === 'users') {
            if (isset($id)) {
                deleteUser($db_connect, $id);
            }
        }
}

echo json_encode([
    "status" => false,
    "message" => "Неверный запрос"
]);


