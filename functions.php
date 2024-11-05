<?php

function getUsers($db_connect) {
    $users = pg_query($db_connect, "SELECT * FROM \"users\"");
    $usersList = [];

    while ($user  = pg_fetch_assoc($users)){
        $usersList[] = $user;
    }
    echo json_encode($usersList);
    pg_close($db_connect);
}

function getUser($db_connect, $id){
    $user = pg_query($db_connect, "SELECT * FROM \"users\" WHERE \"id\" = $id");

    if (pg_num_rows($user) === 0) {
        http_response_code(404);
        $res = [
          "status" => false,
          "message" => "User not found"
        ];
        echo json_encode($res);
    }else{
        $user = pg_fetch_assoc($user);
        echo json_encode($user);
    }
}

function addUser($db_connect, $data) {
    $name = $data['name'];
    $email = $data['email'];
    $time = date('Y-m-d H:i:s');
    $time = "'$time'";

    if (empty($name) || strlen($name) > 255) {
        http_response_code(400);
        echo responseError("Invalid name. Name cannot be empty or longer than 255 characters.");
    }else if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo responseError("Invalid email. Email cannot be empty or invalid.");
    }else {
        $user = pg_query($db_connect, "INSERT INTO users (name, email, created_at) VALUES ('$name', '$email', $time)");

        http_response_code(201);
        $res = [
            "status" => true,
//        "user_id" =>  pg_last_oid($user)
        ];
        echo json_encode($res);
    }
}

function updateUser($db_connect, $id, $data) {
    $name = $data['name'];
    $email = $data['email'];

    // Валидация:
    if (!is_null($name) && (empty($name) || strlen($name) > 255)) {
        http_response_code(400);
        echo responseError("Invalid name. Name cannot be empty or longer than 255 characters.");
    } else  if (!is_null($email) && (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
        http_response_code(400);
        echo responseError("Invalid email. Email cannot be empty or invalid.");
    } else {
        $updateFields = [];
        if (!is_null($name)) {
            $updateFields[] = "name = '$name'";
        }
        if (!is_null($email)) {
            $updateFields[] = "email = '$email'";
        }

        if (empty($updateFields)) {
            return responseError("No fields to update.");
        }

        $query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE users.id = '$id'";
        $result = pg_query($db_connect, $query);

        if ($result) {
            http_response_code(200);
            echo responseSuccess("User updated successfully.");
        } else {
            echo responseError("Error updating user.");
        }
    }
}

function deleteUser($db_connect, $id) {
    pg_query($db_connect, "DELETE FROM users WHERE  users.id = '$id'");
    http_response_code(200);
    $res = [
        "status" => true,
        "message" => "User is deleted"
    ];
    echo json_encode($res);
}

function responseSuccess($message, $data = []) {
    return json_encode([
        "status" => true,
        "message" => $message,
        "data" => $data
    ]);
}

function responseError($message) {
    return json_encode([
        "status" => false,
        "message" => $message
    ]);
}