<?php

include '../prelude.php';

// authorize request
if (empty($_SESSION['username']) || !User::from_username($_SESSION['username'])->is_admin()) {
    http_response_code(401);
}

$user = User::from_userId($_POST['userId']);
$success = $user->delete_user();

if ($success) {
    http_response_code(200);
} else {
    http_response_code(400);
}