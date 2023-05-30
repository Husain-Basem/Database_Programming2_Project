<?php

include '../prelude.php';

$user = User::from_userId($_POST['userId']);
$success = $user->delete_user();

if ($success) {
    http_response_code(200);
} else {
    http_response_code(400);
}
