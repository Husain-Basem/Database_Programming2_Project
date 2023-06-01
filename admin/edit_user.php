<?php

include_once '../prelude.php';

// authorize request
if (empty($_SESSION['username']) || !User::from_username($_SESSION['username'])->is_admin()) {
    http_response_code(401);
}

$user = User::from_userId($_POST['userId']);


if (isset($_POST['username'])) {
    if (User::username_exists($_POST['username'])) {
        http_response_code(400);
        echo 'Username taken';
        exit;
    } else
        $user->userName = $_POST['username'];
}
if (isset($_POST['email']))
    $user->email = $_POST['email'];
if (isset($_POST['type']))
    $user->type = $_POST['type'];

if ($user->update_user())
    http_response_code(200);
else {
    http_response_code(400);
    echo 'Could not update in database';
}